document.addEventListener('DOMContentLoaded', function () {
    const selectField = document.querySelector('select[name="wapf[field_60db2adcc153e]"]');

    if (selectField) {
        const calendarField = document.createElement('input');
        calendarField.type = 'date';
        calendarField.name = selectField.name;
        calendarField.className = selectField.className;
        calendarField.required = selectField.required;

        const availableDates = Array.from(selectField.options)
            .filter(option => option.value) // Ignora a opção vazia
            .map(option => {
                const label = option.dataset.wapfLabel; // Exemplo: "06/01"
                const [day, month] = label.split('/');
                const year = new Date().getFullYear();
                const formattedDate = `${year}-${month}-${day}`;

                calendarField.dataset[formattedDate] = option.value; // Ex.: {"2025-01-06": "0wxtq"}
                return formattedDate;
            });

        calendarField.min = availableDates[0];
        calendarField.max = availableDates[availableDates.length - 1];

        selectField.parentNode.replaceChild(calendarField, selectField);

        calendarField.addEventListener('input', function () {
            const selectedDate = calendarField.value; // Data selecionada no formato YYYY-MM-DD
            if (!availableDates.includes(selectedDate)) {
                alert('A data selecionada não está disponível. Por favor, escolha outra.');
                calendarField.value = ''; // Limpar valor inválido
            } else {
                const mappedValue = calendarField.dataset[selectedDate];
                // console.log('Data selecionada:', selectedDate);
                // console.log('Valor associado:', mappedValue);

                const allTimeFields = document.querySelectorAll('.wapf-field-container[data-wapf-d]');

                allTimeFields.forEach(field => {
                    let dataRules = [];
                    try {
                        dataRules = JSON.parse(field.getAttribute('data-wapf-d') || '[]');
                    } catch (e) {
                        console.error('Erro ao parsear data-wapf-d:', e);
                    }

                    const isRelevant = dataRules.some(rule =>
                        rule.rules.some(r => r.value === mappedValue)
                    );

                    if (isRelevant) {
                        field.classList.remove('wapf-hide');
                        field.classList.add('wapf-exibe');
                        // console.log(field);
                        const timeSelect = field.querySelector('select');
                        if (timeSelect) {
                            timeSelect.removeAttribute('disabled');
                            timeSelect.required = true;
                        }
                    } else {
                        field.classList.add('wapf-hide');
                        const timeSelect = field.querySelector('select');
                        if (timeSelect) {
                            timeSelect.setAttribute('disabled', '');
                            timeSelect.required = false;
                        }
                    }
                });
            }
        });
    }
});


document.addEventListener('DOMContentLoaded', function () {
    const initialPriceElement = document.querySelector('.wpr-product-price .woocommerce-Price-amount bdi');
    let initialPrice = parseFloat(initialPriceElement.textContent.replace(/[^\d,]/g, '').replace(',', '.')) || 0;

    // console.log('Preço inicial:', initialPrice);

    const fieldContainers = document.querySelectorAll('.wapf-field-container');

    fieldContainers.forEach(container => {
        const addonPriceElement = container.querySelector('.wapf-addon-price');
        if (addonPriceElement) {
            const addonPrice = parseFloat(addonPriceElement.textContent.replace(/[^\d,]/g, '').replace(',', '.')) || 0;

            const inputField = container.querySelector('input[type="number"]');

            if (inputField) {
                inputField.addEventListener('input', function () {
                    const quantity = parseInt(inputField.value, 10) || 0;
                    const totalAddonPrice = addonPrice * quantity;
                    recalculateTotalPrice();
                });
            }
        }
    });

    function recalculateTotalPrice() {
        let totalPrice = initialPrice;

        fieldContainers.forEach(container => {
            const addonPriceElement = container.querySelector('.wapf-addon-price');
            const inputField = container.querySelector('input[type="number"]');

            if (addonPriceElement && inputField) {
                const addonPrice = parseFloat(addonPriceElement.textContent.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
                const quantity = parseInt(inputField.value, 10) || 0;
                totalPrice += addonPrice * quantity;
            }
        });

        if (initialPriceElement) {
            initialPriceElement.textContent = `R$ ${totalPrice.toFixed(2).replace('.', ',')}`;
        }
    }
});

