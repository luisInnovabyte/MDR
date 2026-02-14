// Test 2: Cargar solo la estructura base de formularioCliente.js
console.log('TEST 2: Inicio de carga');

$(document).ready(function () {
    console.log('TEST 2: document.ready ejecutado');
    
    var formValidator = new FormValidator('formCliente', {
        codigo_cliente: {
            required: true
        },
        nombre_cliente: {
            required: true
        },
        nif_cliente: {
            required: true
        }
    });
    
    console.log('TEST 2: FormValidator inicializado');
    
    // Test event listener básico
    $(document).on('click', '#btnSalvarCliente', function (event) {
        console.log('TEST 2: BOTON CLICKEADO');
        event.preventDefault();
        alert('El botón funciona!');
    });
    
    console.log('TEST 2: Event listener registrado');
});

console.log('TEST 2: Fin de carga');
