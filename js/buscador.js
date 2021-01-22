/**
* variables globales
*/
let ciudades, tipos, datos, bienes = [];

/**
* obtener datos para select ciudades y tipos
*/
const getInitialData = () => {
    $.ajax({
        url: 'data-1.json',
        type: 'GET',
        data: {},
        success: (data) => {
            datos = data
            ciudades = filterCiudades(data)
            tipos = filterTipos(data)
            selectAppend("#selectCiudad", ciudades)
            selectAppend("#selectCiudadReporte", ciudades)
            selectAppend("#selectTipo", tipos)
            selectAppend("#selectTipoReporte", tipos)
        }
    });
}

/**
* obtener bienes de base de datos 
*/
const getBienes = () => {
    $.ajax({
        url: '../php/consulta_bienes.php',
        type: 'GET',
        data: {},
        success: res => {
            bienes = JSON.parse(res).data;
            $("#divBienes").html("");
            if (bienes.length > 0) {
                $("#length_bienes").text(bienes.length);
                bienes.forEach(result => renderItemResult(result, "#divBienes"))
            }
        }
    });
}

/**
* insertar bienes en base de datos 
*/
const insertNew = resultId => {
    const result = datos.filter(function (e) {
        return e.Id === resultId;
    })[0];
    $.ajax({
        url: '../php/insert.php',
        type: 'POST',
        data: result,
        success: data => {
            if (data === 'success' || data === 'duplicado') {
                $(`.btn_${result.Id}`).hide()
                getBienes()
            }
        }
    });
}

/**
* eliminar bienes en base de datos
* @param {int} Descripción del parámetro
*/
const deleteBien = bienId => {
    $.ajax({
        url: '../php/delete.php',
        type: 'POST',
        data: {bienId},
        success: (data) => {
            if (data === 'success') {
                getBienes()
            }
        }
    });
}

/**
* función para agregar options a selects
* @param {String} select: en donde se agregarán los option
* @param {Array[String]} array: array de elementos
*/
const selectAppend = (select, array) => array.forEach(item => $(select).append(`<option value="${item}"> ${item} </option>`))

/**
* función para filtrar y delolver array de ciudades 
* @param {Array} array de datos generales
* @returns {Array} ciudades filtradas
*/
const filterCiudades = (array) => {
    let ciudades = [];
    array.forEach(item => {
        if (!ciudades.includes(item.Ciudad)) ciudades.push(item.Ciudad)
    });
    return ciudades;
}

/**
* función para filtrar y delolver array de tipos 
* @param {Array} array de datos generales
* @returns {Array} tipos filtrados
*/
const filterTipos = (array) => {
    let tipos = [];
    array.forEach(function (item) {
        if (!tipos.includes(item.Tipo)) tipos.push(item.Tipo)
    });
    return tipos;
}

/**
* función para devolver resultados de busqueda con ciudad, tipo y rango de precio 
* @returns {Array} resultados filtrados
*/
const getResultsSearch = () => {
    const ciudad = $("#selectCiudad").val()
    const tipo = $("#selectTipo").val()
    let precio = $("#rangoPrecio").val()
    precio = precio.split(";")
    return datos.filter(function (item) {
        let Precio = item.Precio.replace("$", "")
        Precio = Precio.replace(",", "")
        return item.Ciudad == ciudad && item.Tipo == tipo && (Precio >= precio[0] && Precio <= precio[1]);
    });
}

/**
* función para dar formato a precio de bienes obtenidos de base de datos
* @param {string} Precio sin formatear
* @returns {number} precio formateado
*/
const formatPrice = precio => {
    //opciones moneda
    //const options = {style: 'currency', currency: 'COP'};
    const numberFormat1 = new Intl.NumberFormat('es-ES');
    return numberFormat1.format(precio);
}

/**
* función para dar formato a precio de bienes obtenidos de base de datos
* @param {map} result: resultado individual de bienes
* @param {string} div: div en el que se mostrará el resultado
* @returns {number} precio formateado
*/
const renderItemResult = (result, div) => {
    const img = "img/home.jpg";
    $(div).append(`
    <div class="contenedor_result">
        <div class="contenedor_img_result">
            <img width="100%" src="${img}" alt="Result">
        </div>
        <div class="contenedor_info_result">
            <ul class="ul_info_result">
                <li>Dirección: <span>${result.Direccion || result.direccion}</span></li>
                <li>Ciudad: <span>${result.Ciudad || result.nombre_ciudad}</span></li>
                <li>Teléfono: <span>${result.Telefono || result.telefono}</span></li>
                <li>Código Postal: <span>${result.Codigo_Postal || result.codigo_postal}</span></li>
                <li>Tipo: <span>${result.Tipo || result.nombre_tipo}</span></li>
                <li>Precio: <span>${result.Precio || '$' + formatPrice(result.precio)}</span></li>
            </ul>
            ${result.id ? `<button class="btn-green btn_delete_${result.id}" onclick="deleteBien(${result.id})">
                    ELIMINAR
                </button>`  
        :`<button class="btn-green btn_${result.Id}" onclick="insertNew(${result.Id})">
                    GUARDAR
                </button>`}
        </div>
    </div>
    `);
}

$(document).ready(() => {
    // ejecutar consultas cuando la página este lista
    getInitialData();
    getBienes();

    // ejecutar busqueda al dar click al boton BUSCAR y mostrarlos en resultados
    $("#submitReporte").click(() => generarReportes());

    $("#submitButton").click(() => {
        const results = getResultsSearch()
        if (results.length === 0){
            $("#length_results").html(0);
            $("#div_results_search").html('');
            return;
        }
        $("#length_results").html(results.length);
        $("#div_results_search").html('');
        results.forEach(result => renderItemResult(result, "#div_results_search"))
    });
});