const NoDataMessage = {
    id: 'emptyChart',

    afterDraw(chart, args, options) {
        const { datasets } = chart.data;
        let hasData = false;

        for(let dataset of datasets) {
            if(dataset.data.length > 0 && dataset.data.some(item => item !== 0)) {
                hasData = true;
                break;
            }
        }

        if(!hasData) {
            const { chartArea: { left, top, right, bottom }, ctx } = chart;
            const centerX = (left + right) / 2;
            const centerY = (top + bottom) / 2;

            chart.clear();
            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('No data to display', centerX, centerY);
            ctx.restore();
        }
    }
};