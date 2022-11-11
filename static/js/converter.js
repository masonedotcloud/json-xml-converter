function copy(div) {
    var copyTextarea = document.querySelector('#' + div.id);
    copyTextarea.focus();
    copyTextarea.select()
}

function update(inizio, fine) {
    var form_data = new FormData();
    codice = document.getElementById(inizio + "-edit").value;
    form_data.append("codice", codice);
    form_data.append("inizio", inizio);
    form_data.append("fine", fine);
    $.ajax({
        url: 'static/php/converter.php',
        method: 'POST',
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () { },
        success: function (data) {
            try {
                response = JSON.parse(data);
                if (response.stato == 'successo') {
                    document.getElementById(fine + '-result').value = response.codice_update;

                } else if (response.stato == 'errore') {
                    document.getElementById(fine + '-result').value = '';
                }
            } catch (error) {
                document.getElementById(fine + '-result').value = '';
            }
        }
    });
}