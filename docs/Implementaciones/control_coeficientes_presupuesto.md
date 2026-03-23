 Controlamos en la tabla articulo el campo coeficiente_articulo, si el valor 0, debes  recoger el campo coeficiente_familia, id_familia, 
Si el campo campo_coeficiente tiene el valor 2 este articulo no va a permitir coeficientes, por lo que volvemos a con ese valor a la pantalla de formularioPresupuesto.php. 
sin embargo si es 1, debemos devolver el valor 1 a la pantallla de formularioPresupuesto.php
Si tiene el valor 0, debemos acudir con el campo id_famlia recogido de la tabla articulo a la tabla familia y recoger en valor de coeficiente_familia, si este presenta el valor 1 devolveremos un 3 y si tiene el valor 0 devolverems un 4.


2.- Articulo - DEvolver 2 por que no permite coeficientes.
1.- Articulo - Devolver 1 por que el el articulo si que permite los coeficientes
3.- Es por que en Articulo el campo es 0 (depende de la familia) aplica los coeficientes (valor 1 en famlias)
4.- Es por que en Articulo el campo es 0 (depende de la familia) NO aplica los coeficientes (valor 0 en familias)

Cable - articulo no permitir coeficientes
KIT - ILU - BASIC - si permitir coeficientes
MIC-INAL-001 - Depende de familia - AUD-MIC - Familia SI permitir
MIX-DIG-X32 - Depende de la familia - VID-PROY - Familia NO pe