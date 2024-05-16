import { reactive } from "vue";
export const state = reactive({
    connected: false,
    fooEvents: [],
    barEvents: []
});

const server = 'wss://gefester.ru:9999';
export const socket  = io.connect(server, {transports: ['websocket']});


socket.on("connect", () => {
    state.connected = true;

});

socket.on("disconnect", () => {
    state.connected = false;
});

socket.on("foo", (...args) => {
    state.fooEvents.push(args);
});

socket.on("bar", (...args) => {
    state.barEvents.push(args);
});


