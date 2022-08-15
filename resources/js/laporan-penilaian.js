import { Grid, h } from 'gridjs';
import 'gridjs/dist/theme/mermaid.min.css';

window.grid = ({ id, columns, data, action }) => {
    new Grid({
        columns: action ? [...columns, {
            name: action.name,
            formatter: (cell, row) => {
                const props = action.builder(row.cells.map(cell => cell.data));
                return h(props.type, {
                    className: props.className,
                    onClick: props.onClick,
                }, props.label);
            }
        }] : columns,
        data: data,
    }).render(document.getElementById(id));
}
