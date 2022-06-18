// ./build_installer.js

// 1. Import Modules
const { MSICreator } = require('electron-wix-msi');
const path = require('path');

// 2. Define input and output directory.
// Important: the directories must be absolute, not relative e.g
// appDirectory: "C:\\Users\sdkca\Desktop\OurCodeWorld-win32-x64", 
const APP_DIR = path.resolve(__dirname, './win32-x64');
// outputDirectory: "C:\\Users\sdkca\Desktop\windows_installer", 
const OUT_DIR = path.resolve(__dirname, './windows_installer');
const LOGO = path.resolve(__dirname, './logo.ico');
const BACKGROUND = path.resolve(__dirname, './background.jpg');
const CERT = path.resolve(__dirname, './firma_codigo.pfx');

// 3. Instantiate the MSICreator
const msiCreator = new MSICreator({
    appDirectory: APP_DIR,
    outputDirectory: OUT_DIR,

    // Configure metadata
    description: 'Sistema de Restaurantes',
    exe: 'eC34-Pedidos',
    name: 'eC34-Pedidos',
    manufacturer: 'Sistemas C34',
    version: '1.0.0',
    cultures: 'es-es',
    language: 1034,
    appIconPath: LOGO,

    // Configure installer User Interface
    ui: {
        chooseDirectory: true,
        images: {
            background: BACKGROUND,
        },
    },
    features: {
        autoUpdate: true,
    },
    certificateFile: CERT,
    certificatePassword: 'fredy123',
});

// 4. Create a .wxs template file
msiCreator.create().then(function(){

    // Step 5: Compile the template to a .msi file
    msiCreator.compile();
});