document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.star-rating .fa-star');

    function setStars(rating) {
        stars.forEach(star => {
            star.classList.remove('checked');
            if (star.getAttribute('data-rating') <= rating) {
                star.classList.add('checked');
            }
        });
    }

    stars.forEach(star => {
        star.addEventListener('click', function () {
            const rating = this.getAttribute('data-rating');
            document.getElementById('rating').value = rating;
            setStars(rating);
        });

        star.addEventListener('mouseover', function () {
            setStars(this.getAttribute('data-rating'));
        });

        star.addEventListener('mouseout', function () {
            setStars(document.getElementById('rating').value);
        });
    });
});
