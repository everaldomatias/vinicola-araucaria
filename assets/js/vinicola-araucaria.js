jQuery( document.body ).on( 'added_to_cart removed_from_cart', function(){
    jQuery( document.body ).trigger( 'wc_fragment_refresh' )
})

document.addEventListener('DOMContentLoaded', function() {
    const adultSelect = document.getElementById('adult_price')
    const childSelect = document.getElementById('child_price')
    const productID = window.VA_data.product_id
    const baseUrl = window.VA_data.base_url

    function fetchNewPrice() {
        const adults = adultSelect.value
        const children = childSelect.value

        fetch(`${baseUrl}/vinicolaaraucaria/v1/calculate-price/?product_id=${productID}&adults=${adults}&children=${children}`)
            .then(response => response.json())
            .then(data => {
                const priceContainer = document.querySelector('.recalculate-total-price')
                priceContainer.innerHTML = data.total
            })
            .catch(error => console.error('Error fetching new price:', error))
    }

    if (adultSelect && childSelect && productID) {
        adultSelect.addEventListener('change', fetchNewPrice)
        childSelect.addEventListener('change', fetchNewPrice)
    }

})