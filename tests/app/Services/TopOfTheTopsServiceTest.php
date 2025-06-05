<?php

namespace Tests\app\Services;

use Mockery;
use PHPUnit\Framework\TestCase;
use App\Services\TopOfTheTopsService;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Repositories\DBRepositories;
use App\Exceptions\ServerErrorException;
use App\Exceptions\TwitchUnauthorizedException;

class TopOfTheTopsServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function returnsFromDatabaseWhenEnoughRecordsExist()
    {
        $twitchMock = Mockery::mock(TwitchApiRepositoryInterface::class);

        $sampleRow = [
            'game_id'                   => '2',
            'game_name'                 => 'GameTwo',
            'user_name'                 => 'StreamerB',
            'total_videos'              => 3,
            'total_views'               => 8000,
            'most_viewed_title'         => 'Mega Clip',
            'most_viewed_views'         => 5000,
            'most_viewed_duration'      => '00:15:00',
            'most_viewed_created_at'    => '2025-06-02 14:30:00',
            'updated_at'                => '2025-06-02 14:30:00',
        ];
        $dbMock = Mockery::mock(DBRepositories::class);
        $dbMock
            ->shouldReceive('getRecentTopVideos')
            ->once()
            ->with(Mockery::type('string'))
            ->andReturn([
                $sampleRow,
                $sampleRow,
                $sampleRow,
            ]);

        $dbMock->shouldNotReceive('existsTopGame');
        $dbMock->shouldNotReceive('insertTopGame');
        $dbMock->shouldNotReceive('upsertTopVideo');

        $service = new TopOfTheTopsService($twitchMock, $dbMock);
        $result = $service->getTopVideos(3600);

        $this->assertCount(3, $result);
        foreach ($result as $row) {
            $this->assertSame('2', $row['game_id']);
            $this->assertSame('StreamerB', $row['user_name']);
        }
    }

    /** @test */
    public function queriesTwitchAndPersistsWhenNotEnoughDatabaseRecords()
    {
        putenv('TWITCH_TOKEN=valid_token');

        $twitchMock = Mockery::mock(TwitchApiRepositoryInterface::class);

        $dbMock = Mockery::mock(DBRepositories::class);
        $dbMock
            ->shouldReceive('getRecentTopVideos')
            ->once()
            ->with(Mockery::type('string'))
            ->andReturn([
                [
                    'game_id'                => '3',
                    'user_name'              => 'X',
                    'total_videos'           => 1,
                    'total_views'            => 100,
                    'most_viewed_title'      => 'A',
                    'most_viewed_views'      => 50,
                    'most_viewed_duration'   => '00:05:00',
                    'most_viewed_created_at' => '2025-06-03 10:00:00',
                    'updated_at'             => '2025-06-03 10:00:00',
                ],
                [
                    'game_id'                => '3',
                    'user_name'              => 'Y',
                    'total_videos'           => 1,
                    'total_views'            => 200,
                    'most_viewed_title'      => 'B',
                    'most_viewed_views'      => 150,
                    'most_viewed_duration'   => '00:07:00',
                    'most_viewed_created_at' => '2025-06-03 11:00:00',
                    'updated_at'             => '2025-06-03 11:00:00',
                ],
            ]);

        $fakeGames = [
            ['id' => '10', 'name' => 'GameTen'],
            ['id' => '20', 'name' => 'GameTwenty'],
            ['id' => '30', 'name' => 'GameThirty'],
        ];
        $twitchMock
            ->shouldReceive('getTopGames')
            ->once()
            ->with('valid_token', 3)
            ->andReturn($fakeGames);

        $twitchMock
            ->shouldReceive('getVideosByGame')
            ->times(3)
            ->andReturn([]);

        $aggregatedForGame10 = [
            [
                'game_id'                => '10',
                'game_name'              => 'GameTen',
                'user_name'              => 'User10',
                'total_videos'           => 2,
                'total_views'            => 300,
                'most_viewed_title'      => 'Clip10',
                'most_viewed_views'      => 200,
                'most_viewed_duration'   => '00:08:00',
                'most_viewed_created_at' => '2025-06-04T12:00:00Z',
            ],
        ];
        $aggregatedForGame20 = [
            [
                'game_id'                => '20',
                'game_name'              => 'GameTwenty',
                'user_name'              => 'User20',
                'total_videos'           => 1,
                'total_views'            => 150,
                'most_viewed_title'      => 'Clip20',
                'most_viewed_views'      => 150,
                'most_viewed_duration'   => '00:05:00',
                'most_viewed_created_at' => '2025-06-04T13:00:00Z',
            ],
        ];
        $aggregatedForGame30 = [];

        $twitchMock
            ->shouldReceive('aggregateVideosByUser')
            ->once()
            ->with([], '10', 'GameTen')
            ->andReturn($aggregatedForGame10);
        $twitchMock
            ->shouldReceive('aggregateVideosByUser')
            ->once()
            ->with([], '20', 'GameTwenty')
            ->andReturn($aggregatedForGame20);
        $twitchMock
            ->shouldReceive('aggregateVideosByUser')
            ->once()
            ->with([], '30', 'GameThirty')
            ->andReturn($aggregatedForGame30);

        $dbMock
            ->shouldReceive('existsTopGame')
            ->once()
            ->with('10')
            ->andReturn(false);
        $dbMock
            ->shouldReceive('insertTopGame')
            ->once()
            ->with('10', 'GameTen')
            ->andReturn(true);

        $dbMock
            ->shouldReceive('existsTopGame')
            ->once()
            ->with('20')
            ->andReturn(true);

        $dbMock
            ->shouldReceive('existsTopGame')
            ->once()
            ->with('30')
            ->andReturn(false);
        $dbMock
            ->shouldReceive('insertTopGame')
            ->once()
            ->with('30', 'GameThirty')
            ->andReturn(true);

        $dbMock
            ->shouldReceive('upsertTopVideo')
            ->once()
            ->with(Mockery::on(function ($videoData) {
                return $videoData['most_viewed_created_at'] === date('Y-m-d H:i:s', strtotime('2025-06-04T12:00:00Z'))
                    && $videoData['game_id'] === '10'
                    && $videoData['user_name'] === 'User10';
            }))
            ->andReturn(true);

        $dbMock
            ->shouldReceive('upsertTopVideo')
            ->once()
            ->with(Mockery::on(function ($videoData) {
                return $videoData['most_viewed_created_at'] === date('Y-m-d H:i:s', strtotime('2025-06-04T13:00:00Z'))
                    && $videoData['game_id'] === '20'
                    && $videoData['user_name'] === 'User20';
            }))
            ->andReturn(true);

        $service = new TopOfTheTopsService($twitchMock, $dbMock);
        $result = $service->getTopVideos(1800);

        $this->assertCount(2, $result);

        $first = $result[0];
        $this->assertSame('10', $first['game_id']);
        $this->assertSame('User10', $first['user_name']);
        $this->assertSame('Clip10', $first['most_viewed_title']);
        $this->assertSame('GameTen', $first['game_name']);
        $this->assertSame('2025-06-04 12:00:00', $first['most_viewed_created_at']);

        $second = $result[1];
        $this->assertSame('20', $second['game_id']);
        $this->assertSame('User20', $second['user_name']);
        $this->assertSame('2025-06-04 13:00:00', $second['most_viewed_created_at']);
    }

}
