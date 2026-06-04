// СЛАЙДЕР: 4 картинки, автосмена каждые 3 секунды
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

// МАСКА ТЕЛЕФОНА: 8(XXX)XXX-XX-XX
document.querySelectorAll('.phone-mask').forEach(input => {
  input.addEventListener('input', event => {
    // 1) Берём только цифры, максимум 11 штук
    let digits = event.target.value.replace(/\D/g, '').slice(0, 11);

    // 2) Если первая цифра не "8" — заменяем (например, ввели "+7" → станет "8")
    if (digits.length && digits[0] !== '8') {
      digits = '8' + digits.slice(0, 10);
    }

    // 3) Собираем по маске 8(XXX)XXX-XX-XX
    let result = '';
    if (digits.length >= 1) result = digits[0];
    if (digits.length >= 2) result += '(' + digits.slice(1, 4);
    if (digits.length >= 4) result += ')' + digits.slice(4, 7);
    if (digits.length >= 7) result += '-' + digits.slice(7, 9);
    if (digits.length >= 9) result += '-' + digits.slice(9, 11);

    event.target.value = result;
  });
});
