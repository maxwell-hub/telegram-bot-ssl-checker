const MONTHS = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
];

class DateFormat {
    formatDate(date) {
        let d = new Date(date);
        return MONTHS[d.getMonth()]
            + ' '
            + d.getDate()
            + ', '
            + d.getFullYear();
    }
}

const _DateFormat = new DateFormat();

export default _DateFormat;