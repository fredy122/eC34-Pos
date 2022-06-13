const electron = require('electron')
const path = require('path')
const electronDl = require('electron-dl');
let progressInterval

const { clipboard } = require('electron')

var net = require('net');
var server = net.createServer();

const BrowserWindow = electron.BrowserWindow
const app = electron.app

let myWindow = null
const gotTheLock = app.requestSingleInstanceLock()

// esto debe colocarse en la parte superior de main.js para manejar los eventos de configuración rápidamente
if (handleSquirrelEvent(app)) {
    // evento de squirrel manejado y la aplicación se cerrará en 1000 ms, así que no hagas nada más
    return;
}

//Usamos electronDl para descarga directa
electronDl({
    //overwrite para sobre escribir el archivo
    //por defecto todas las descargas sera en la carpeta de descarga por defecto
    overwrite:true
});

//
//Validados que que no exista una instancia en ejecución
if (!gotTheLock) {
    app.quit()
}else{
    app.on('ready', () => {
        createWindow()
    })
}

var phpServer = require('node-php-server');
const port = 8881, host = '127.0.0.1';
const serverUrl = `http://${host}:${port}`;

let mainWindow

function createWindow() {
    // Create a PHP Server
    phpServer.createServer({
        port: port,
        hostname: host,
        base: `${__dirname}/www/public`,
        keepalive: false,
        open: false,
        bin: `${__dirname}/php/php.exe`,
        router: __dirname + '/www/server.php'
    });

    // Create the browser window.
    const {
            width,
            height
        } = electron.screen.getPrimaryDisplay().workAreaSize 
        mainWindow = new BrowserWindow({
        width: width,
        height: height,
        webPreferences: {
            preload: path.join(__dirname, 'preload.js')
        },
        show: false,
        autoHideMenuBar: true,
        movable: false,
        resizable: false,
    })

    //pogress de barra 
    /*
    const INCREMENT = 0.03
    const INTERVAL_DELAY = 100 // ms

    let c = 0
    progressInterval = setInterval(() => {
        // update progress bar to next value
        // values between 0 and 1 will show progress, >1 will show indeterminate or stick at 100%
        mainWindow.setProgressBar(c)

        // increment or reset progress bar
        if (c < 2) {
            c += INCREMENT
        } else {
            c = (-INCREMENT * 5) // reset to a bit less than 0 to show reset state
        }
    }, INTERVAL_DELAY)

    app.on('before-quit', () => {
        clearInterval(progressInterval)
    })
    */
    
    mainWindow.loadURL(serverUrl)
    mainWindow.webContents.once('dom-ready', function () {
        mainWindow.show()
        mainWindow.maximize();
        // mainWindow.webContents.openDevTools()
    });

  // Emitted when the window is closed.
  mainWindow.on('closed', function () {
    phpServer.close();
    mainWindow = null;
  })
}

// This method will be called when Electron has finished
// initialization and is ready to create browser windows.
// Some APIs can only be used after this event occurs.
//app.on('ready', createWindow) // <== this is extra so commented, enabling this can show 2 windows..

// Quit when all windows are closed.
app.on('window-all-closed', function () {
  // On OS X it is common for applications and their menu bar
  // to stay active until the user quits explicitly with Cmd + Q
  if (process.platform !== 'darwin') {
    // PHP SERVER QUIT
    phpServer.close();
    app.quit();
  }
})

app.on('activate', function () {
  // On OS X it's common to re-create a window in the app when the
  // dock icon is clicked and there are no other windows open.
  if (mainWindow === null) {
    createWindow()
  }
})

function handleSquirrelEvent(application) {
    if (process.argv.length === 1) {
        return false;
    }

    const ChildProcess = require('child_process');
    const path = require('path');

    const appFolder = path.resolve(process.execPath, '..');
    const rootAtomFolder = path.resolve(appFolder, '..');
    const updateDotExe = path.resolve(path.join(rootAtomFolder, 'Update.exe'));
    const exeName = path.basename(process.execPath);

    const spawn = function(command, args) {
        let spawnedProcess, error;

        try {
            spawnedProcess = ChildProcess.spawn(command, args, {
                detached: true
            });
        } catch (error) {}

        return spawnedProcess;
    };

    const spawnUpdate = function(args) {
        return spawn(updateDotExe, args);
    };

    const squirrelEvent = process.argv[1];
    switch (squirrelEvent) {
        case '--squirrel-install':
        case '--squirrel-updated':
            // Opcionalmente, haga cosas como:
            // - Agregue su .exe a la RUTA
            // - Escriba en el registro para cosas como asociaciones de archivos y
            //   menús contextuales del explorador

            // Instalar accesos directos del menú de inicio y del escritorio
            spawnUpdate(['--createShortcut', exeName]);

            setTimeout(application.quit, 1000);
            return true;

        case '--squirrel-uninstall':
            // Deshace todo lo que hiciste en el --squirrel-install y
            // --squirrel-updated handlers

            // Eliminar los accesos directos del escritorio y del menú de inicio
            spawnUpdate(['--removeShortcut', exeName]);

            setTimeout(application.quit, 1000);
            return true;

        case '--squirrel-obsolete':
             // Esto se llama en la versión saliente de su aplicación antes
             // Actualizamos a la nueva versión - es lo contrario de
             // --update squirrel

            application.quit();
            return true;
    }
};

