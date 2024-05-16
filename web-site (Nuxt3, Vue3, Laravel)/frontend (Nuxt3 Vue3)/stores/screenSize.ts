import { defineStore } from 'pinia';

export const useScreenSizeStore = defineStore('screenSize', () => {
    const windowSize = ref(process.client ? window.innerWidth : 0);

    function setObserverResize() {
        if (process.client) {
                window.addEventListener("resize", () => {
                    windowSize.value = window.innerWidth;
                });
            };
    }
    function unSetObserverResize() {
        if (process.client) {
            window.removeEventListener("resize", () => {
                windowSize.value = window.innerWidth;
            });
        };
    }

    const screenSize = computed(() => {
        if (windowSize.value > 1024) {
            return "large";
        } else if (windowSize.value < 640) {
            return "small";
        } else if (windowSize.value < 1024) {
            return "medium";
        } else {
            return "large";
        }
    });

    return {
        setObserverResize,
        unSetObserverResize,
        screenSize,
        windowSize
    };
})
