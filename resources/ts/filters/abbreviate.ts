import Vue from 'vue';

const nsAbbreviate = Vue.filter('abbreviate', (value) => {
    if (typeof value !== 'number') value = Number(value) || 0;

    if (value < 1000) return 'Rp ' + value.toLocaleString('id-ID');

    const suffixes = ['', 'ribu', 'juta', 'miliar', 'triliun'];
    const suffixNum = Math.floor( ('' + value).length / 3 );
    let shortValue;

    // Cari angka pendek dengan precision 1 atau 2
    for (let precision = 2; precision >= 1; precision--) {
        shortValue = parseFloat(
            suffixNum !== 0
                ? (value / Math.pow(1000, suffixNum)).toPrecision(precision)
                : value.toPrecision(precision)
        );
        const dotLess = (shortValue + '').replace(/[^a-zA-Z0-9]+/g, '');
        if (dotLess.length <= 2) break;
    }

    // Format decimal ke 1 angka di belakang koma
    if (shortValue % 1 !== 0) shortValue = shortValue.toFixed(1);

    // Ganti titik desimal dengan koma (format Indonesia)
    const formatted = String(shortValue).replace('.', ',');

    return 'Rp ' + formatted + ' ' + suffixes[suffixNum];
});

export { nsAbbreviate };
