import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    vus: 5, // 5 віртуальних користувачів
    duration: '30s', // тест триватиме 30 секунд
};

const BASE_URL = 'http://localhost'; // твій локальний домен

export default function () {
    // 1. Відкриваємо сторінку логіну
    let res = http.get(`${BASE_URL}/login`);
    check(res, { 'login page loaded': (r) => r.status === 200 });

    // 2. Авторизація (POST запит)
    // Тобі треба вказати дані реального юзера, якого ти створив для Dusk
    res = http.post(`${BASE_URL}/login`, {
        email: 'admin@example.com',
        password: 'password',
    });
    check(res, { 'logged in successfully': (r) => r.status === 302 || r.status === 200 });

    // 3. Перегляд допису (наприклад, ID = 1)
    res = http.get(`${BASE_URL}/posts/1`);
    check(res, { 'post viewed': (r) => r.status === 200 });

    // 4. Вихід (Logout)
    res = http.post(`${BASE_URL}/logout`);
    check(res, { 'logged out': (r) => r.status === 302 || r.status === 200 });

    sleep(1); // пауза між циклами користувача
}
