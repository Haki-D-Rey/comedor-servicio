class Util {

  fechaActual = null;
  constructor() {
    this.fechaActual = "";
  }

  formatDate(timezoneOffset, dateFormat) {
    const date = new Date();

    const adjustedDate = new Date(
      date.getTime() + timezoneOffset * 60 * 60 * 1000
    );

    let dateString =
      adjustedDate.toISOString().split("T")[0] +
      " " +
      adjustedDate.toISOString().split("T")[1].split(".")[0];

    let [datePart, timePart] = dateString.split(" ");

    const [year, month, day] = datePart.split("-");
    const [hours, minutes, seconds] = timePart.split(":");

    let formattedDate = dateFormat
      .replace("YYYY", year)
      .replace("MM", month)
      .replace("DD", day)
      .replace("HH", hours)
      .replace("mm", minutes)
      .replace("ss", seconds);

    this.fechaActual = formattedDate;
    return this.fechaActual;
  }
}

export default Util;