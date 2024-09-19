
import { ref, toRef } from 'vue'
import { useAxios } from './useAxios'
import type { AxiosError, AxiosResponse } from 'axios'


export const useHttpService = () => {
    const { http } = useAxios()
    const processing = ref<boolean>(false);

    function get(url: string, config = {}): Promise<AxiosResponse | void | any> {
        // Optional (if we have a centralized loader to show)
        processing.value = true;
        return http.get(url, config)
            .then((response) => {
                return response?.data
            })
            .catch((error: AxiosError) => {
                return false
            })
            .finally(() => {
                processing.value = false;
            });
    }


    return {
        get, processing
    }
}