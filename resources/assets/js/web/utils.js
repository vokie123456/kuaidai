let Utils = {
    formatAmount(amount) {
        if (amount < 1000) {
            return amount * 1;
        } else if (amount < 10000) {
            return (amount / 1000) + '千';
        } else {
            return (amount / 10000) + '万';
        }
    }
};

export default Utils;