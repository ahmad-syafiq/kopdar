window.createLineChart = function (selector) {
  const $el = $(selector);

  return new Morris.Line({
    element: $el.attr("id"),
    resize: true,
    data: $el.data("json"),
    xkey: $el.data("json-x"),
    ykeys: $el.data("json-y"),
    labels: $el.data("json-label"),
    lineColors: $el.data("json-color"),
    hideHover: 'auto',
    ymin: 0,
    parseTime: false,

    xLabelFormat: x => x.label.split("-")[2],
    yLabelFormat: y => y.toLocaleString("en-US"),

    hoverCallback: function (index, options, content, row) {
      return content.replace(
        /(morris-hover-row-label'>).*?(<\/div>)/,
        `$1${row[options.xkey]}$2`
      );
    }
  });
};

