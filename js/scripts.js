$(document).ready(function() {
    $('#search-form').on('submit', function(e) {
        e.preventDefault();
        const params = {
            modelo_coche: $('input[name="modelo_coche"]').val(),
            marca: $('select[name="marca"]').val(),
            'nombre-pieza': $('input[name="nombre-pieza"]').val()
        };

        window.location.href = `mvcvistas/search.php?${$.param(params)}`;
    });
});