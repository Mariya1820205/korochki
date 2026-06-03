// Простой слайдер: 4 картинки, автосмена каждые 3 секунды
let slides = document.querySelectorAll('.slide');
let current = 0;

function show(index) {
  slides.forEach(slide => slide.classList.remove('active'));
  current = (index + slides.length) % slides.length;
  slides[current].classList.add('active');
}

function move(direction) {
  show(current + direction);
}

if (slides.length) {
  setInterval(() => move(1), 3000);
}
