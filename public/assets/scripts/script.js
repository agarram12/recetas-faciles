document.addEventListener('DOMContentLoaded', () => {

    // ---------------------------------------------------------
    // 1. BASE DE DATOS SIMULADA (Tus Recetas)
    // ---------------------------------------------------------
    const recetasDB = [
        {
            id: 1,
            autor: "Pat Kim",
            avatar: "assets/img/usuario1.png", // Asegúrate de que esta ruta exista o usa placeholder
            fecha: "Hace 4h",
            titulo: "Tarta de queso casera",
            descripcion: "¡Buenas chicos! He estado experimentando con la receta de tarta de queso definitiva. ¡Creo que encontré el punto perfecto! 🧀🍰",
            imagen: "./assets/img/cheesecake.jpg",
            likes: 28,
            likedByMe: false,
            comentarios: 12
        },
        {
            id: 2,
            autor: "Chef Mario",
            avatar: "assets/img/usuario1.png",
            fecha: "Hace 6h",
            titulo: "Costillas BBQ estilo Texas",
            descripcion: "¿Domingo de asado? Aquí os dejo mi secreto para las costillas perfectas. Fuego lento y paciencia. 🔥🍖 #BBQ",
            imagen: "./assets/img/bbq.jpg",
            likes: 156,
            likedByMe: true,
            comentarios: 45
        },
        {
            id: 3,
            autor: "Laura Veggie",
            avatar: "assets/img/usuario1.png",
            fecha: "Hace 1d",
            titulo: "Bowl de Buda Saludable",
            descripcion: "Para compensar el fin de semana, nada mejor que un bowl lleno de nutrientes, aguacate y garbanzos. 🥑🥗 #Healthy",
            imagen: "./assets/img/salad.jpg",
            likes: 89,
            likedByMe: false,
            comentarios: 20
        }
    ];

    // ---------------------------------------------------------
    // 2. LÓGICA DEL FEED (Solo se ejecuta en feed.html)
    // ---------------------------------------------------------
    const feedContainer = document.getElementById('feedContainer');
    const searchInput = document.getElementById('searchInput');
    const noResults = document.getElementById('noResults');

    if (feedContainer) {
        
        // Función para pintar las tarjetas
        function renderRecetas(lista) {
            feedContainer.innerHTML = ''; // Limpiamos para no duplicar

            if (lista.length === 0) {
                if(noResults) noResults.classList.remove('d-none');
                return;
            }
            
            if(noResults) noResults.classList.add('d-none');

            lista.forEach(receta => {
                // Icono del corazón (Relleno si le di like)
                const heartIcon = receta.likedByMe ? 'bi-heart-fill text-danger' : 'bi-heart';
                
                // HTML de la tarjeta
                const cardHTML = `
                <article class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center border-0 py-3">
                        <div class="d-flex align-items-center">
                            <img src="${receta.avatar}" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;" alt="Avatar">
                            <div>
                                <h6 class="mb-0 fw-bold">${receta.autor}</h6>
                                <small class="text-muted" style="font-size: 0.8rem;">${receta.fecha}</small>
                            </div>
                        </div>
                        <button class="btn btn-link text-muted"><i class="bi bi-three-dots"></i></button>
                    </div>

                    <div class="card-body p-0">
                        <p class="px-3 mb-2">${receta.descripcion}</p>
                        <a href="detalleReceta.html"> <img src="${receta.imagen}" class="img-cover w-100" style="max-height: 400px;" alt="Plato">
                        </a>
                    </div>

                    <div class="card-body">
                        <h5 class="fw-bold">${receta.titulo}</h5>
                        
                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-outline-danger flex-grow-1 rounded-pill action-like" data-id="${receta.id}">
                                <i class="bi ${heartIcon}"></i> Me gusta
                            </button>
                            
                            <a href="detalleReceta.html" class="btn btn-primary flex-grow-1 rounded-pill text-white text-decoration-none">
                                Ver paso a paso <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>
                `;

                feedContainer.innerHTML += cardHTML;
            });
        }

        // Evento Buscador
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const texto = e.target.value.toLowerCase();
                const filtradas = recetasDB.filter(r => 
                    r.titulo.toLowerCase().includes(texto) || 
                    r.descripcion.toLowerCase().includes(texto) ||
                    r.autor.toLowerCase().includes(texto)
                );
                renderRecetas(filtradas);
            });
        }

        // Evento Likes (Delegación)
        feedContainer.addEventListener('click', (e) => {
            const btnLike = e.target.closest('.action-like');
            if (btnLike) {
                // Animación visual simple del like sin recargar todo
                const icon = btnLike.querySelector('i');
                if (icon.classList.contains('bi-heart')) {
                    icon.classList.remove('bi-heart');
                    icon.classList.add('bi-heart-fill', 'text-danger');
                } else {
                    icon.classList.add('bi-heart');
                    icon.classList.remove('bi-heart-fill', 'text-danger');
                }
            }
        });

        // Carga inicial
        renderRecetas(recetasDB);
    }

});