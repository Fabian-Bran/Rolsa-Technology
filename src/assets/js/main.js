
const box = document.getElementById("box");
const button = document.getElementById("animate");

button.addEventListener("click", () => {

    box.style.transform = "translateY(0) rotate(0deg)";

    engine.animate({
        from: 0,
        to: 250,
        duration: 1500,
        easing: "elastic",

        update: value => {
            box.style.transform =
                `translateY(${-value}px) rotate(${value * 0.5}deg)`;
        },

        complete: () => {
            console.log("Animation completed!");
        }
    });

});
