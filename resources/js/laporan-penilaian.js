import { Grid, h } from 'gridjs';
import 'gridjs/dist/theme/mermaid.min.css';

window.grid = ({ id, columns, data, action, styles }) => {
    columns = columns.map((column) => ({
        name: column,
        attributes: (cell) => {
            return {
                'data-cell-content': cell,
                'style': styles ? styles[cell] : '',
            }
        }
    }));
    if (action) {
        columns.push({
            name: action.name,
            formatter: (cell, row) => {
                const props = action.builder(row.cells.map(cell => cell.data));
                return h(props.type, {
                    className: props.className,
                    onClick: props.onClick,
                }, props.label);
            }
        });
    }


    return new Grid({
        columns,
        data: data ?? [],
    }).render(document.getElementById(id));
}
