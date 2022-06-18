## Acerca del proyecto

Interface adaptable hasta tablets para gestionar pedidos con lineas y sublineas de productos

En el archivo .env hemos agregado la variable APP_TYPE para identificar como van a accesar al sistema

-APP_TYPE=local : ya no pedira numero RUC, no es necesario intalar mysql ya que se conectara directamente al servidor SQL SERVER
-APP_TYPE=web : pedira numero RUC para buscar el ip de su servidor en nuestra base de datos mysql, la base de datos mysql que estamos usando es ec34_pedidos


## Campos en base de datos
- En la tabla Agentes necesitamos agregar el campo "c_rutaa char(5)" para resgistrar a los clientes nuevos

	ALTER TABLE agentes
	ADD c_rutaa char(5) not null default('');

- Pa imprimir las pre cuentas desde el pos necesitamos agregar el nombre de la impreso en la tabla sisprop en el campo despre5 
ojo: el nombre de la impresora no el nombre de algun archivo

## Para Imprimir Pre-Cuenta y Comanda directo a cocina
- Para las impresiones hemos usado registros de windows(.reg=regedit) para ejecutar los .exe de impresion desde el navegador, a esto se le llama "protocol handle", windows por default crea una copia de los archivos de texto que usamos para indicar la impresora y el archivo a imprimir ( impresora.txt, impresorak.txt, fileaimp.txt ) en la siguiente ruta:

-esta ruta es referencial, puede variar la ultima parte y en el nombre de usuario:

	C:\Users\RMEDINA\AppData\Local\VirtualStore\Program Files (x86)\Google\Chrome\Application\63.0.3239.84

-sino encontramos la ruta anterior solo buscamos en el disco disco se cualquiera de los archivos, por ejemplo: "impresora.txt" y nos dara la hubicacion.

-tambien podemos usar el siguiente atajo windows+r -> %appdata%


## Protocol Handler ( "ec34tabtip:" , "ec34printicket")

- En algunoas computadoras no se esta guardando las preferencias de usuario de google chrome y siempre que ejecutamos estos protocols "handlers personalizados" esta preguntando si queremos ejecutar el comando, pero esto es molesto para el usuario, la solucion es dirigirse a la carpteta
	
-Nota: la carpeta AppData se encuentra oculta, por lo que tenemos que configurar para que se pueda visualizar

	C:\Users\JECHABAUDIS\AppData\Local\Google\Chrome\User Data\Default -> Preferences

	en las versionas antiguas se hubica en 

	C:\Users\JECHABAUDIS\AppData\Local\Google\Chrome\User Data -> Local State

-ahora hubicamos el archivo "Preferences" y buscamos la linea "excluded_schemes", este es un archivo de objeto tipo javascript y solo agregamos en su matriz el siguiente valor para que no este preguntando a cada rato el navegador: 

	"ec34tabtip":false o "ec34printicket":false 

-La matriz quedaria asi masomenos, pero pueden haber otros protocols-handlers de otras aplicaciones como por ejemplo "mailto"

	{"excluded_schemes":{"ec34tabtip":false,"ec34printicket":false}}

## Cómo permitir que Google Chrome sobrescriba los archivos descargados 

- Instalar la extension de google chrome: "Downloads Overwrite Already Existing Files"

## Ocultar barra de descargas de google chrome 

- Instalar la extension de google chrome: "disabled download bar"

## Instalacion =====================================================================================================================================

+ Despues de clonar o descargar el proyecto nos dirigimos a su ruta:

		$ cd nombreDelProyecto

+ Ejecutamos el siguiente comando de Laravel

		$ composer install

+ Modificamos el nombre del archivo __.env.example.__ por __.env__ y agregamos nuestras credenciales.

+ Ahora debemos de generar una key para nuestra aplicacion.

		$ php artisan key:generate

+ Ahora ya podemos ejecutar el proyecto

		$ php artisan serve

## Para desarrollo

+ Si queremos modificar los archivos de javascript tenemos que instalar los módulos de node para compilar

		$ npm install

+ Para compilar en modo desarrollo

		$ npm run watch

## Para producción

+ Para compilar para producción(este comando minificara los archivos javascript y CSS, ademas bloqueara que la extensión de vue js en el navegador Chrome se habilite)

		$ npm run prod

## Comandos laravel para despliegue en servidor producción y mejorar el performance
		
		$ git pull origin master (aqui puedes traer los cambios de la rama que se este usando como principal)

		$ php artisan route:cache

		$ php artisan optimize --force