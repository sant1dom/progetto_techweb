function removeAlert () {
    setTimeout(function () {
        $('div.alert').slideUp(200).alert('close');
    }, 3000);
}