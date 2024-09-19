// useHttpStore.ts
import axios from 'axios'



export const useAxios = () => {
    const http = axios.create({
        withCredentials: true,
        responseType: 'json',
        headers: {
            'Accept': 'application/json',
            'Content-type': 'application/json'
        }
    })

    return { http }
}