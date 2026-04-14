export function useFormatRupiah() {
    const formatRupiah = (amount: number): string => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(amount);
    };

    const discountPercent = (original: number, promo: number): number => {
        return Math.round(((original - promo) / original) * 100);
    };

    return { formatRupiah, discountPercent };
}
