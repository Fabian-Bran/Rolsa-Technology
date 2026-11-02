
class AnimationEngine {

    constructor() {
        this.animations = [];
        this.running = false;
        this.lastTime = null;
    }

    // Easing functions
    easing = {
        linear: t => t,

        easeIn: t => t * t,

        easeOut: t => 1 - Math.pow(1 - t, 3),

        easeInOut: t =>
            t < 0.5
                ? 4 * t * t * t
                : 1 - Math.pow(-2 * t + 2, 3) / 2,

        elastic: t =>
            t === 0 || t === 1
                ? t
                : Math.pow(2, -10 * t) *
                  Math.sin((t * 10 - 0.75) *
                  (2 * Math.PI / 3)) + 1,

        bounce: t => {
            const n = 7.5625;
            const d = 2.75;

            if (t < 1 / d) {
                return n * t * t;
            }

            if (t < 2 / d) {
                return n * (t -= 1.5 / d) * t + 0.75;
            }

            if (t < 2.5 / d) {
                return n * (t -= 2.25 / d) * t + 0.9375;
            }

            return n * (t -= 2.625 / d) * t + 0.984375;
        }
    };

    animate({
        from,
        to,
        duration = 1000,
        delay = 0,
        easing = "easeOut",
        update,
        complete = () => {}
    }) {
        this.animations.push({
            from,
            to,
            duration,
            delay,
            easing,
            update,
            complete,
            elapsed: 0,
            completed: false
        });

        if (!this.running) {
            this.running = true;
            this.lastTime = null;
            requestAnimationFrame(this.loop);
        }
    }

    loop = (time) => {

        if (this.lastTime === null) {
            this.lastTime = time;
        }

        const delta = Math.min(time - this.lastTime, 50);
        this.lastTime = time;

        this.animations = this.animations.filter(anim => {

            if (anim.completed) return false;

            anim.elapsed += delta;

            if (anim.elapsed < anim.delay) {
                return true;
            }

            const progress = Math.min(
                (anim.elapsed - anim.delay) / anim.duration,
                1
            );

            const ease = this.easing[anim.easing] ||
                         this.easing.linear;

            const value = anim.from +
                (anim.to - anim.from) * ease(progress);

            anim.update(value, progress);

            if (progress >= 1) {
                anim.completed = true;
                anim.complete();
                return false;
            }

            return true;
        });

        if (this.animations.length > 0) {
            requestAnimationFrame(this.loop);
        } else {
            this.running = false;
            this.lastTime = null;
        }
    };
}

const engine = new AnimationEngine();
