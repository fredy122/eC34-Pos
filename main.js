const electron = require('electron')
const path = require('path')
const electronDl = require('electron-dl');
let progressInterval

var net = require('net');
var server = net.createServer();
//esta es la prueba 2 con servidor personal

const BrowserWindow = electron.BrowserWindow
const app = electron.app
const autoUpdater = electron.autoUpdater

const serverUpdate = "https://e-c34-l9q6dhho2-fredy122.vercel.app"
const feed = `${serverUpdate}/update/${process.platform}/${app.getVersion()}`
autoUpdater.setFeedURL(feed)


setInterval(() => {
    autoUpdater.checkForUpdates()
}, 60000)

autoUpdater.on('update-downloaded', (event, releaseNotes, releaseName) => {
    const dialogOpts = {
        type: 'info',
        buttons: ['Restart', 'Ahora no. En el próximo reinicio'],
        title: 'Update',
        message: process.platform === 'win32' ? releaseNotes : releaseName,
        detail: 'Se ha descargado una nueva versión. Reinicie ahora para completar la actualización.'
    }

    dialog.showMessageBox(dialogOpts).then((returnValue) => {
        if (returnValue.response === 0) autoUpdater.quitAndInstall()
    })
})

autoUpdater.on('error', message => {
    console.error('Hubo un problema al actualizar la aplicación.')
    console.error(message)
})

const gotTheLock = app.requestSingleInstanceLock()

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
            preload: path.join(__dirname, 'preload.js'),
            enableRemoteModule: false,
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
        //mainWindow.webContents.openDevTools()
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