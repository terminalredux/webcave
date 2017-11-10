jQuery.extend(jQuery.validator.messages, {
    required: "To pole jest wymagane.",
    remote: "Popraw to pole.",
    email: "Proszę podać poprawny adres email.",
    url: "Proszę podać poprawny adres URL.",
    date: "Proszę podać poprawną datę.",
    dateISO: "Proszę podać poprawną datę (ISO).",
    number: "Proszę podać numer.",
    digits: "Proszę podać tylko cyfry.",
    creditcard: "Proszę podać poprawny numer karty.",
    equalTo: "Proszę podać tą samą wartość.",
    accept: "Proszę podać nazwę pliku z poprawnym rozszerzeniem.",
    maxlength: jQuery.validator.format("Maksymalna ilość znaków {0}."),
    minlength: jQuery.validator.format("Minimalna ilość znaków: {0}."),
    rangelength: jQuery.validator.format("Długość musi być pomiędzy {0} a {1}."),
    range: jQuery.validator.format("Podaj wartość pomiędzy {0} a {1}."),
    max: jQuery.validator.format("Podaj wartość mniejszą bądź równą {0}."),
    min: jQuery.validator.format("Podaj wartość równą lub większą od {0}.")
});
