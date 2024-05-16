import { defineStore } from "pinia";
import { ref } from "vue";
import axios from "@/plugins/http";

export const useKkmStore = defineStore("kkm", () => {
  /* Механика работы сервер-ккм 
  // при оплате в родителе 0.1 isFiscalNumber - Подготовка проверка в LS есть ли успешный чек по этому заказу с неудачным коммитом
  // при оплате в родителе 0.2  createRequestPayments() - Подготовка: формирование запроса на сервер для оплаты
  1 step. makePayment() - Запрос на сервер данных для запроса на ККМ /cashdesk/pay/start || или getInfo, если не нужен коммит
  2 step. kkmAction() - Запрос на ККМ на печать чека
  3 step. verifyKkm() - Запрос на ККМ по UUID- проверка результата запроса на печать чека (получаем fiscalDocumentNumber, который нужен для повторной печати чека)
  4 step. verifyServer()- Запрос на сервер - подтверждение успешного запроса на ККМ (передаем fiscalDocumentNumber ) /cashdesk/pay/commit
    4.1 saveFiscalNumber() - если commit не удачный, то записываем в Locale Storage (order_id, UUID, fiscalDocumentNumber)
    4.2 reVerifyServer() - повторная переотправка коммитов

  
  Тесты ККМ
  1. Тест оплаты с депозитом
      + electronically и prepaid
      + cash и prepaid
      + сумма депоизта меньше заказа
      + cумма депозита больше заказа
  2. Тест ошибки от сервера и от касссы
      + отправка на другой url сервера (вывод - Ошибка сервера (404))
      + отправка на другой url ккм (вывод - Ошибка в работе ККМ)
      + неправильный payments (чек аннулируется на ккм + вывод Ошибка в работе ККМ)
      +сохранение неудачных коммитов и переотправка из store
  
  */

  const dialogKkmMessage = ref("ККМ в работе");

  //технические переменные
  const loadingPayment = ref(false);
  const isKkmSuccess = ref(false);
  const isServerSuccess = ref(false);
  let fiscalDocumentNumber: number = 0;
  const checkInfo = {};
  // const checkInfo: { order_id: number } = {};
  let urlCommit: string = "";

  /* delay для работы с ККМ 
  Для чего:
1 дожидаемся печати чека, чтобы получить fiscalDocumentNumber 
2 показываем клиенту 3 сек, что чек успешно напечатан, после чего переводим на другую страницу
 */
  function delay(ms: number) {
    return new Promise((resolve) => setTimeout(resolve, ms));
  }

  //step 1
  async function makePayment(
    bodyRequest: any,
    urlServerStart: string,
    urlServerCommit: string = ''
  ) {
    urlCommit = urlServerCommit;
    dialogKkmMessage.value = "ККМ в работе";
    isKkmSuccess.value = false;
    isServerSuccess.value = false;
    try {
      loadingPayment.value = true;
      const { data } = await axios.post(urlServerStart, bodyRequest);
      if (!data) {
        dialogKkmMessage.value = "Ошибка сервера";
        return;
      }
      //step 2
      await kkmAction(data);
      //ждем отработки step 2,3,4
      if (!isKkmSuccess.value || !isServerSuccess.value) return;
      //если step 2,3,4 прошли успешно - показываем 3сек "Чек напечатан" и переводим на другую страницу
      dialogKkmMessage.value = "Чек напечатан";
    } catch (error: any) {
      dialogKkmMessage.value = `Ошибка сервера (${error.response.status})`;
      console.log(error);
    }
  }

  //step 2 kkm
  async function kkmAction(data: object) {
    if (!data) return (dialogKkmMessage.value = `Ошибка сервера`);

    try {
      const response = await fetch(data.cashdesk.url, {
        method: "post",
        headers: data.cashdesk.headers,
        body: JSON.stringify(data.cashdesk.body),
      });
      if (response.ok) {
        //step 3 через 2.5сек,чтобы дождаться завершения печати чека
        await delay(2500);
        await verifyKkm(data);

        if (isKkmSuccess.value) {
          //step 4
          await verifyServer(data.data.id);
        }
      } else {
        //пример ошибки: касса не подлкючена к компу/занят порт прокси сервера
        throw new Error("KKM unavailable");
      }
      // вовзращаемся в step 1
    } catch (error) {
      console.log(error);
      dialogKkmMessage.value = "Ошибка в работе ККМ";
      isKkmSuccess.value = false;
    }
  }

  //step 3
  async function verifyKkm(data: any) {
    try {
      const responseKkm = await fetch(
        `${data.cashdesk.url}/${data.cashdesk.body.uuid}`,
        {
          method: "get",
          headers: data.cashdesk.headers,
        }
      );
      const dataKkm = await responseKkm.json();
      console.log(
        `наличие ошибок от ккм: code: ${dataKkm.results[0].error.code}, описание: ${dataKkm.results[0].error.description}`
      );

      // проверка наличия ошибок в работе ККМ, если 'code: 0', то все ок
      if (dataKkm.results[0].error.code !== 0) {
        // если неи, генерируем ошибку
        throw new Error(
          `Error code: ${dataKkm.results[0].error.code}, описание: ${dataKkm.results[0].error.description}`
        );
      }
      //ожидание окончания печати чека, чтобы получить fiscalDocumentNumber
      if (dataKkm.results[0].status == "inProgress") {
        await verifyKkm(data);
      }
      // проверка успешного создания чека
      if (
        dataKkm.results[0].error.code === 0 &&
        dataKkm.results[0].status === "ready"
      ) {
        isKkmSuccess.value = true;
        
        if (dataKkm.results[0].result ===  null) return;
        
        fiscalDocumentNumber = dataKkm.results[0].result.fiscalParams.fiscalDocumentNumber;
        
      }
      //возвращаемя в step 2 и вызываем step 4
    } catch (error) {
      console.log(error);
      dialogKkmMessage.value = "Ошибка в работе ККМ";
      isKkmSuccess.value = false;
    }
  }

  //step 4
  async function verifyServer(id: number) {
    if (!id) return;
    localStorage.removeItem("fiscalNumber");

    let data = {};

    switch (urlCommit) {
      case '':
        return;
      case "/cashdesk/shift/close/commit":
      case "/cashdesk/shift/open/commit":
        data = {
          id: id,
        };
        break;
      case "/cashdesk/pay/commit":
      case "/cashdesk/deposit/commit":
      case "/cashdesk/refund/commit":
        data = {
          receipt_id: id,
          number: fiscalDocumentNumber,
        };
        break;
    }

    try {
    console.log(data);
      await axios.post(urlCommit, data);
      isServerSuccess.value = true;
    } catch (error) {
      console.log(error);
      isServerSuccess.value = false;
      dialogKkmMessage.value = `Ошибка сервера`;
      saveFiscalNumber();
    } finally {
      loadingPayment.value = false;
    }
  }
  //step 4.1
  //получаем текущее данные неудачных коммиттов, после успешной печати чека и записываем новые
  function saveFiscalNumber() {
    //localStorage.removeItem("fiscalNumber"); */ //для теста
    // const value = JSON.parse(localStorage.getItem("fiscalNumber"));
    const fiscalNumber = localStorage.getItem("fiscalNumber")
    if (!fiscalNumber) return localStorage.setItem("fiscalNumber", JSON.stringify([checkInfo]))
    const value: { order_id: number }[] = JSON.parse(fiscalNumber);
    console.log(value);
    if (value == null) {
      localStorage.setItem("fiscalNumber", JSON.stringify([checkInfo]));
    } else {
      for (let i = 0; i < value.length; i++) {
        //проверка, что уже сохранен
        if (value[i].order_id === checkInfo.order_id) {
          return;
        }
      }
      value.push(checkInfo);
      localStorage.setItem("fiscalNumber", JSON.stringify(value));
    }

    if (!isReVerifyServer) {
      //запускаем в случае, если она сейчас не работает ,чтобы не перегрузить сервер
      reVerifyServer();
    }
  }

  //step 4.2 - повторная переотправка коммитов
  let isReVerifyServer: boolean = false;
  async function reVerifyServer() {
    //получаем из localStorage массив неудачных коммитов
    // const value = JSON.parse(localStorage.getItem("fiscalNumber"));
    const fiscalNumber = localStorage.getItem("fiscalNumber")
    if (!fiscalNumber) return localStorage.setItem("fiscalNumber", JSON.stringify(checkInfo))
    const value = JSON.parse(fiscalNumber)
    const successCommits: any = [];

    if (value == null) {
      return;
    } else {
      //выполняем повторную отправку комммитов
      isReVerifyServer = true;
      for (const el of value) {
        try {
          await axios.post("/cashdesk/pay/commit", {
            receipt_id: el.receipt_id,
            number: el.fiscal_number,
          });
          successCommits.push(el);
        } catch (error) {
          console.log(error);
        }
      }
      //еще раз получаем данные из LS, на случай если в момент работы функции появились новые неудачные коммиты
      const revalue = JSON.parse(fiscalNumber);
      if (successCommits.length == 0) {
        //нет успешных отправленных коммитов, пробуем снова через 5 сек
        setTimeout(() => {
          reVerifyServer();
        }, 5000);
      } else {
        //сравниваем с массивом успешных комитов и убираем успешно отправленные, в result остаются только неотправленные коммиты
        const result = revalue.filter(
          (item1: any) =>
            !successCommits.some(
              (item2: any) => item2.order_id === item1.order_id
            )
        );

        if (result.length == 0) {
          //все коммиты из LS отпрваились успешно
          localStorage.removeItem("fiscalNumber");
          isReVerifyServer = false;
        } else {
          //остались неудачные коммиты, перезаписываем в LS и пробуем отправить снова
          localStorage.setItem("fiscalNumber", JSON.stringify(result));
          setTimeout(() => {
            reVerifyServer();
          }, 5000);
        }
      }
    }
  }

  //step 1 Для печати предчека и повторной печати чека, где не нужен commit на сервер
  async function getInfo(url: string) {
    dialogKkmMessage.value = "ККМ в работе";
    isKkmSuccess.value = false;
    isServerSuccess.value = false;
    urlCommit =  '';
    try {
      loadingPayment.value = true;
      const { data } = await axios.get(url);
      if (!data) {
        dialogKkmMessage.value = "Ошибка сервера";
        return;
      }
      //step 2
      await kkmAction(data);
      //ждем отработки step 2,3
      if (!isKkmSuccess.value) return;
      //если step 2,3 прошли успешно - показываем 3сек "Чек напечатан" и переводим на другую страницу
      dialogKkmMessage.value = "Чек напечатан";
    } catch (error) {
      dialogKkmMessage.value = `Ошибка сервера (${error.response.status})`;
      console.log(error);
    }

  }

  return {
    makePayment,
    dialogKkmMessage,
    reVerifyServer,
    isKkmSuccess,
    isServerSuccess,
    delay,
    getInfo,
  };
});
