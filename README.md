El proyecto tiene la siguiente estructura de archivos:

    -auth.php: Archivo para autenticar usuarios mediante token.
    
    -config.php: Archivo para establecer la conexión con nuestra base de datos.

    -obtenerToken.php: Archivo para obtener el token

    -registerUser.php: Archivo para gestionar el registro de usuarios y la generación de una APIkey única.

    -index.php: Archivo principal para el manejo de rutas.

    -funcionesComunes.php: Archivo que contiene funciones comunes utilizadas en el proyecto.

    -src/: Carpeta que contiene los siguientes archivos:

        -consultarStreamer.php: Se utiliza para el primer caso (consultar información de un streamer).
        -consultarStream.php: Se utiliza para el segundo caso (consultar streams en vivo).
        -consultarEnriquecidos.php: Se utiliza para el tercer caso (consultar streams enriquecidos).
        -topOfTheTops.php: Se utiliza para el nuevo endpoint (consultar los 40 videos mas visualizados de los 3 juegos más populares en Twitch).


Para ejecutar cualquiera de los casos hay 2 opciones:

**1.Ejecutarlo en nuestra pagina web(introducir en tu navegador el enlace descrita a continuación): https://easymoneyvyv.es/analytics**

Antes de acceder a los casos se debe registrar en primer lugar con un email, para hacerlo se debe hacer a traves del la web postman y de la siguiente forma:

Se debe realizar una peticion de tipo POST a la url https://easymoneyvyv.es/analytics/register y en al apartado body se selecciona raw y se debe escribir con el siguiente formato el email para que sea válido:
```json
    {
        "email": "Email que quieres registrar"
    }
```
La respuesta del servidor a la consulta sera de la forma:
```json
    {
        "api_key": "Tu api key generada"
    }
```
Debes guardar la api key para a continuación obtener el token que te permita acceder a los casos de uso, para ello se debe realizar una petición de tipo POST a la url https://easymoneyvyv.es/analytics/token y en la sección body se selecciona raw y se debe escribir con el siguiente formato el email y la api key para que sea válido:
```json
    {
        "email": "Email registrado",
        "api_key": "Tu api key"
    }
```
La respuesta del servidor a la consulta sera de la forma:
```json
    {
        "token": "Tu token genererado"
    }
```
Por ultimo para acceder a cualquier de los siguientes casos de uso en el postman, la peticion debe ser de tipo GET y se debe añadir en la sección header uno nuevo, en el apartado key debe ser de tipo "X-Auth-Token" y en value debes introducir "Bearer tokenGenerado" en caso de probarlo en nuestra pagina, si lo vas a utilizar en otro servidor puedes introducir tanto el anterior header como
en key "Authorization" y en value "Bearer tokenGenerado".


Para esta opción, disponemos de 4 enlaces posibles, uno para cada caso de uso propuesto:

    -Para acceder al primer caso se debe acceder a la url https://easymoneyvyv.es/analytics/user?id="ID DEL STREAMER", donde debe sustituir el campo "ID DEL STREAMER" por la ID de twitch del streamer que desea consultar. Para obtener una ID válida puede convertir el nombre de un streamer a una ID válida en el siguiente enlace: https://www.streamweasels.com/tools/convert-twitch-username-%20to-user-id/

    -Para acceder al segundo caso debe acceder a la url https://easymoneyvyv.es/analytics/streams

    -Para acceder al tercera caso debe acceder a la url https://easymoneyvyv.es/analytics/streams/enriched?limit="NUMERO DE STREAMS A MOSTRAR", donde debe sustituir el campo "NUMERO DE STREAMS A MOSTRAR" por el límite de streams que desea consultar.

    -Para acceder al tercera caso debe acceder a la url https://easymoneyvyv.es/analytics/streams/topOfTheTops. 
    También existe la posibilidad de ejecutarlo con un parámetro "since" para forzar la actualización más reciente de Twitch.
    La url es de este tipo: https://easymoneyvyv.es/analytics/streams/topOfTheTops?since="Numero que quieras".

**2.Ejecutarlo en un servidor local o "localhost":**

Para esta opción, el proceso a seguir es el siguiente:

    1-Descargar los archivos en su PC.
    2-Instalar XAMPP, disponible en este enlace: https://www.apachefriends.org/es/index.html
    3-Una vez instalado, ir al directorio donde se encuentre la carpeta XAMPP (por defecto en Windows: C), dentro de XAMPP dirigirse a la carpeta htdocs y pegar los archivos descargados dentro de esta.
    4-Iniciar XAMPP Control Panel y posteriormente activar el servidor Apache (boton Start).
    
    REQUISITO: La base de datos de XAMPP obviamente no es la misma que la nuestra entonces hay que cambiar de config.php la direccion de la base de datos para hacerlo en local.

Para obtener en el api_key y el token en vez de usar el postman deberas realizar los comandos por terminal a https://localhost/analytics/register añadiendo el body explicado anteriormente
al igual que para obtener el token a https://localhost/analytics/token añadiendo el body explicado anteriormente y para los siguientes casos también debes añadir alguno de los header explicados anteriormente.

Una vez cumplimentado lo anterior, disponemos de estos 4 enlaces posibles, uno para cada caso de uso propuesto:

    -Para acceder al primer caso se debe acceder a la url https://localhost/analytics/user?id="ID DEL STREAMER", donde debe sustituir el campo "ID DEL STREAMER" por la ID de twitch del streamer que desea consultar. Para obtener una ID válida puede convertir el nombre de un streamer a una ID válida en el siguiente enlace: https://www.streamweasels.com/tools/convert-twitch-username-%20to-user-id/

    -Para acceder al segundo caso debe acceder a la url https://localhost/analytics/streams

    -Para acceder al tercera caso debe acceder a la url https://localhost/analytics/streams/enriched?limit="NUMERO DE STREAMS A MOSTRAR", donde debe sustituir el campo "NUMERO DE STREAMS A MOSTRAR" por el límite de streams que desea consultar.

    - -Para acceder al tercera caso debe acceder a la url https://localhost/analytics/streams/topOfTheTops. 
    También existe la posibilidad de ejecutarlo con un parámetro "since" para forzar la actualización más reciente de Twitch.
    La url es de este tipo: https://easymoneyvyv.es/analytics/streams/topOfTheTops?since="Segundos que quieras".