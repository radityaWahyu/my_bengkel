// useHttpStore.ts
export const usePrice = () => {
    const convertToRupiah = (price: number) =>
        new Intl.NumberFormat("en-ID", {
            style: "currency",
            currency: "IDR",
        }).format(price);

    return { convertToRupiah }
}