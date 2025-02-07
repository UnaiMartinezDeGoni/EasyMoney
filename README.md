El proyecto tiene la siguiente estructura de archivos:

    -index.php: Archivo principal para el manejo de rutas.

    -funcionesComunes.php: Archivo que contiene funciones comunes utilizadas en el proyecto.

    -src/: Carpeta que contiene los siguientes archivos:

        -consultarStreamer.php: Se utiliza para el primer caso (consultar información de un streamer).
        -consultarStream.php: Se utiliza para el segundo caso (consultar streams en vivo).
        -consultarEnriquecidos.php: Se utiliza para el tercer caso (consultar streams enriquecidos).
        
Para ejecutar cualquiera de los casos hay 2 opciones:

**1.Ejecutarlo en nuestra pagina web(introducir en tu navegador el enlace descrita a continuación): https://easymoneyvyv.es/index.php/analytics

Para esta opción, disponemos de 3 enlaces posibles, uno para cada caso de uso propuesto:

    -Para acceder al primer caso se debe acceder a la url https://easymoneyvyv.es/index.php/analytics/user?id="ID DEL STREAMER", donde debe sustituir el campo "ID DEL STREAMER" por la ID de twitch del streamer que desea consultar. Para obtener una ID válida puede convertir el nombre de un streamer a una ID válida en el siguiente enlace: https://www.streamweasels.com/tools/convert-twitch-username-%20to-user-id/

    -Para acceder al segundo caso debe acceder a la url https://easymoneyvyv.es/index.php/analytics/streams

    -Para acceder al tercera caso debe acceder a la url https://easymoneyvyv.es/index.php/analytics/streams/enriched?limit="NUMERO DE STREAMS A MOSTRAR", donde debe sustituir el campo "NUMERO DE STREAMS A MOSTRAR" por el límite de streams que desea consultar.

**2.Ejecutarlo en un servidor local o "localhost": 

Para esta opción, el proceso a seguir es el siguiente:
    1-Descargar los archivos en su PC.
    2-Instalar XAMPP, disponible en este enlace: https://www.apachefriends.org/es/index.html
    3-Una vez instalado, ir al directorio donde se encuentre la carpeta XAMPP (por defecto en Windows: C), dentro de XAMPP dirigirse a la carpeta htdocs y pegar los archivos descargados dentro de esta.
    4-Iniciar XAMPP Control Panel y posteriormente activar el servidor Apache (boton Start).

Una vez cumplimentado lo anterior, disponemos de estos 3 enlaces posibles, uno para cada caso de uso propuesto:

    -Para acceder al primer caso se debe acceder a la url https://localhost/index.php/analytics/user?id="ID DEL STREAMER", donde debe sustituir el campo "ID DEL STREAMER" por la ID de twitch del streamer que desea consultar. Para obtener una ID válida puede convertir el nombre de un streamer a una ID válida en el siguiente enlace: https://www.streamweasels.com/tools/convert-twitch-username-%20to-user-id/

    -Para acceder al segundo caso debe acceder a la url https://localhost/index.php/analytics/streams

    -Para acceder al tercera caso debe acceder a la url https://localhost/index.php/analytics/streams/enriched?limit="NUMERO DE STREAMS A MOSTRAR", donde debe sustituir el campo "NUMERO DE STREAMS A MOSTRAR" por el límite de streams que desea consultar.

