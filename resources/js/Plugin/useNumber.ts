// useHttpStore.ts
export const usePrice = () => {
    const convertToRupiah = (price: number) =>
        new Intl.NumberFormat("en-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(price);

    return { convertToRupiah }
}