let lastSpotId = 0;

const beep = new Audio("assets/sounds/beep.mp3");
beep.volume = 0.35;

function loadSpots() {

    fetch("api/spots.php")
        .then(response => response.text())
        .then(html => {

            const table = document.getElementById("spotsLive");

            if (!table) return;

            table.innerHTML = html;

            const first = table.querySelector("tr[data-id]");

            if (!first) return;

            const id = parseInt(first.dataset.id);

            if (lastSpotId === 0) {
                lastSpotId = id;
                return;
            }

            if (id > lastSpotId) {

                lastSpotId = id;

                beep.play().catch(() => {});

                first.classList.add("newSpot");

                setTimeout(() => {

                    first.classList.remove("newSpot");

                },5000);

            }

        });

}

loadSpots();

setInterval(loadSpots,5000);