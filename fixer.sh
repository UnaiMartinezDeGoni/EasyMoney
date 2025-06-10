!/bin/bash

changed_files=$(git diff --cached --name-only --diff-filter=ACM | grep '^src/.*\.php$')

if [ -n "$changed_files" ]; then
    echo "Analizando los siguientes archivos en src/:"
    echo "$changed_files"

    ./vendor/bin/php-cs-fixer fix --dry-run --diff --config=.php-cs-fixer.dist.php $changed_files
else
    echo "No hay archivos PHP modificados en src/."
fi
