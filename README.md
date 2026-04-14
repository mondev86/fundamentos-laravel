# Laravel Prep - App de Estudio para Pruebas Técnicas de Laravel

![Laravel](https://img.shields.io/badge/Laravel-11.x-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-purple)
![License](https://img.shields.io/badge/License-MIT-green)

Aplicación interactiva para estudiar fundamentos de Laravel y prepararse para pruebas técnicas y entrevistas.

## Contenido

- [Demo](#demo)
- [Instalación](#instalación)
- [Manual de Uso](#manual-de-uso)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Errores Comunes y Soluciones](#errores-comunes-y-soluciones)
- [Mejoras para Escalar](#mejoras-para-escalar)
- [Tecnologías Usadas](#tecnologías-usadas)

---

## Instalación

```bash
# Colocar en directorio Laravel existente
composer install

# Generar clave de aplicación
php artisan key:generate

# Limpiar cachés
php artisan optimize:clear

# Iniciar servidor
php artisan serve
```

Abre `http://localhost:8000`

---

## Manual de Uso

### 1. Página de Inicio
- Ver todos los temas disponibles
- Acceder al Quiz general
- Entrar al Simulador de Entrevista

### 2. Estudiar Temas
- Click en cualquier tarjeta de tema
- Leer teoría con ejemplos de código
- Revisar **Tips de entrevista** (importante para entrevistas reales)
- Hacer el **quiz interactivo** al final de cada tema
- Navegar entre temas con botones prev/next

### 3. Quiz General
- Mezcla preguntas de todos los temas
- Orden aleatorio
- Feedback inmediato (verde/rojo)
- Resultado final con porcentaje

### 4. Modo Entrevista
- Preguntas abiertas tipo entrevista
- Responder mentalmente antes de ver respuesta
- Ver respuesta sugerida clickeando

---

## Estructura del Proyecto

```
app/
├── Data/
│   └── LaravelData.php         # Contenido: temas, quiz, preguntas entrevista
├── Http/Controllers/
│   ├── HomeController.php      # Página principal
│   ├── TopicController.php    # Ver tema individual
│   └── QuizController.php     # Quiz general + entrevista

resources/views/
├── layouts/app.blade.php       # Layout base con Tailwind
├── home.blade.php              # Landing con cards de temas
├── topics/show.blade.php       # Vista de tema individual + mini quiz
├── quiz/
│   ├── index.blade.php         # Quiz general completo
│   └── interview.blade.php     # Simulador de preguntas abiertas
```

---

## Errores Comunes y Soluciones

### Error: "Class 'App\Data\LaravelData' not found"

**Causa:** El namespace no coincide con la ubicación.

**Solución:**
```bash
composer dump-autoload
```

### Error: Target class [xxxxx] does not exist

**Causa:** Algún binding o facade no está disponible.

**Solución:**
```bash
php artisan config:clear
php artisan cache:clear
```

### Las vistas no cargan / están en blanco

**Causa:** Caché de vistas antigua.

**Solución:**
```bash
php artisan view:clear
```

### Alpine.js no funciona (los quizzes no responden)

**Causa:** CDN no carga o hay conflicto con JavaScript.

**Revisar:**
- Conexión a internet (CDN de Alpine.js)
- Consola del navegador (F12) para errores JS

### Error en `json_encode` / `jsonencode`

**Causa:** La función `jsonencode` (con una palabra) no existe en PHP.

**Solución:** Usar `json_encode` (con guión bajo):
```php
{{ json_encode($variable) }}
```

---

## Mejoras para Escalar

### Nivel 1: Básico (Inmediato)
- [ ] Agregar más temas (Events, Notifications, API Resources)
- [ ] Agregar más preguntas al quiz
- [ ] Guardar progreso en localStorage (temas visitados)

### Nivel 2: Funcionalidades
- [ ] Sistema de usuario con login (Auth)
- [ ] Guardar puntuación del quiz en DB
- [ ] Panel de progreso por usuario
- [ ] Marcar temas como "estudiados"

### Nivel 3: Interactivo
- [ ] Agregar código ejecutable (Laravel Playground embebido)
- [ ] Modo oscuro/claro (toggle)
- [ ] Filtro por nivel (básico/intermedio/avanzado)
- [ ] Buscador de temas

### Nivel 4: Profesional
- [ ] API REST con Laravel + Vue/React
- [ ] Móvil: Convertir a PWA o app con Flutter
- [ ] Sistema de puntos y logros (gamificación)
- [ ] Exportar a PDF el resumen de cada tema
- [ ] Modo "entrevista" con timer (simular presión)

### Mejoras Técnicas
- [ ] Tests (PHPUnit) para los controllers
- [ ] Usar base de datos SQLite en vez de contenido hardcodeado
- [ ] Separar contenido a archivo JSON o DB
- [ ] Internacionalización (i18n) para otros idiomas
- [ ] Deploy a producción (Vercel, Railway, Forge)

---

## Tecnologías Usadas

| Tecnología | Uso |
|-------------|-----|
| Laravel 11 | Framework PHP |
| Blade | Motor de plantillas |
| Alpine.js | Interactividad (quiz, accordions) |
| Tailwind CSS | Estilos (CDN) |
| Laravel Data | Contenido estructurado |

---

## Inspiración

Diseñado para desarrolladores que preparan entrevistas técnicas de Laravel. Enfocado en:
- Preguntas frecuentes en entrevistas
- Conceptos fundamentales que siempre preguntan
- Práctica interactiva con feedback inmediato

---

## Licencia

MIT License - Libre para usar y modificar.