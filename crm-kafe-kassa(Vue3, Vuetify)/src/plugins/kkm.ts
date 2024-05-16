import axios from "axios";
import router from "@/router";
import { HttpStatusCodes } from "@/constatnts/httpStatusCodes";

const kkm = axios.create({
  baseURL: import.meta.env.VITE_KKM_URL,
});

kkm.defaults.baseURL = import.meta.env.VITE_KKM_URL;
kkm.defaults.headers.post["Content-Type"] = "application/json";
kkm.defaults.headers.post["Accept"] = "application/json";
kkm.defaults.headers.common["Authorization"] =
  "Basic YWRtaW46YWRtaW5BRE1JTjEyMzQ=";

const LOGIN_PATH = "/login";

kkm.interceptors.request.use(
  async (config) => {
    // const token = "YWRtaW46YWRtaW5BRE1JTjEyMzQ=";

    // kkm.defaults.headers.common["Authorization"] = "Basic " + token;

    return config;
  },
  function (error) {
    return Promise.reject(error);
  }
);

kkm.interceptors.response.use(
  function (response) {
    return response;
  },
  async function (error) {
    if (
      error.response?.status === HttpStatusCodes.FORBIDDEN ||
      error.response?.status === HttpStatusCodes.UNAUTHORIZED
    ) {
      await router.push(LOGIN_PATH);
    }

    return Promise.reject(error);
  }
);

export default kkm;
