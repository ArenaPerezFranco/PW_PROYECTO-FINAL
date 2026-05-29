document.getElementById('contenedor').addEventListener('submit', async function(e){
    const usuario = document.getElementById('usuario').value;
    const pass = document.getElementById('password').value;
    const error = document.getElementById('message');
    error.textContent = ""; //Limpiar errores anteriores

    //Empaquetamos datos para enviar por PHP
    const datos = new FormDara();
    datos.append('usuario', usuario);
    datos.append('pass', password);

})

//REMEMBER ME BUTTON
