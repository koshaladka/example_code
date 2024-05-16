import {defineStore} from 'pinia';
import to = gsap.to;

export const useLocationStore = defineStore('location', () => {
  const config = useRuntimeConfig()

  const address = ref();
  const country = ref();
  //область, регион
  const province = ref();
  //район в области
  const area = ref();
  const town = ref();
  const coords = ref();
  //выаолнение кода на клиенте
  const isClient = computed(() => process.client);
  const isGeolocationEnabled = computed(() => navigator.geolocation);

  const getLocation = async () => {
    try {
      if (isGeolocationEnabled && isClient) {
        // получаем координаты
        await new Promise<void>((resolve, reject) => {
          navigator.geolocation.getCurrentPosition(
              (position) => {
                const { latitude, longitude } = position.coords;
                coords.value = { latitude, longitude };
                resolve();
              },
              (error) => {
                console.error('Error getting geolocation:', error);
                reject(error);
              }
          );
        });

        // делаем запрос
        const { data } = await useFetch(
            `https://geocode-maps.yandex.ru/1.x/?apikey=${config.public.apiKeyYandex}&format=json&geocode=${coords.value.longitude},${coords.value.latitude}`
        );
        const responseData = data._value.response.valueOf();

        if (responseData.GeoObjectCollection?.featureMember?.[0]?.GeoObject) {
          const geoObject = responseData.GeoObjectCollection.featureMember[0].GeoObject;
          console.log(geoObject);

          // Извлекаем адрес
          address.value = geoObject.metaDataProperty?.GeocoderMetaData?.text || '';

          const components = geoObject.metaDataProperty?.GeocoderMetaData?.Address?.Components;

          // Извлекаем страну -  компонент с kind === 'country'
          const countryComponent = components?.find(component => component.kind === 'country');
          country.value = countryComponent?.name || '';

          // Извлекаем бласть, регион -  компонент с kind === 'province', с проверкой на последний компонент
          const provinceComponents = components?.filter(component => component.kind === 'province');
          const lastProvinceComponent = provinceComponents?.[provinceComponents.length - 1];
          province.value = lastProvinceComponent?.name || '';

          // Извлекаем район -  компонент с kind === 'area'
          const areaComponent = components?.find(component => component.kind === 'area');
          area.value = areaComponent?.name || '';

          // Извлекаем город -  компонент с kind === 'locality'
          const localityComponent = components?.find(component => component.kind === 'locality');
          town.value = localityComponent?.name || '';

          console.log(`страна ${country.value}, регион ${province.value} , область ${area.value}, город ${town.value},  координаты ${JSON.stringify(coords.value)} ` )
        } else {
          console.error('Неверный формат ответа от Yandex Maps API');
        }
      }
    } catch (error) {
      console.error('Ошибка при получении местоположения:', error);
    }
  };


  return {
    isClient,
    getLocation,
    country,
    province,
    area,
    town,
    coords,
  };
})
