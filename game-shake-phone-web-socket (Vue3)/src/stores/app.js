import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { io } from "socket.io-client";
import {socket} from "@/socketIO";
import {validate} from "uuid";


export const useAppStore = defineStore('app', () => {
  // Данные игрока
  const gender = ref('');
  const setGender = (data) => {
    gender.value = data;
  };
  const name = ref('');
  const setName = (data) => {
    name.value = data;
  };
  const id = ref('');
  const setId = (UUID) => {
    id.value = UUID;
  };
  const playerNumber = ref(1);
  const setPlayerNumber = (number) => {
    playerNumber.value = number;
  };

  const isGame = ref(false);
  function setIsGame(bool) {
    isGame.value = bool;
  }

  const place = ref();
  function setPlace(number) {
    place.value = number;
  }

  return {
    gender,
    setGender,
    playerNumber,
    setPlayerNumber,
    name,
    setName,
    id,
    setId,
    setIsGame,
    place,
    setPlace
  }
})
