function correcaoInputMonetario(input) {
    let value = input.target.value.replace(/[^\d]/g, '');
    if (value.length > 0) {
        value = (parseInt(value) / 100).toFixed(2);
        value = value.replace('.', ',');
        input.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
}

function correcaoBlurInputMonetario(input) {
    if (input.target.value === '') {
        input.target.value = '0,00';
    }
}