<div class="panel panel-bordered">
    <div class="panel-heading">
        <h3 class="panel-title">{{ $this->title }}</h3>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        @foreach ($this->columns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->rows as $row)
                        <tr>
                            <td>{{ $row->nama }}</td>
                            <td>{{ $row->tipe }}</td>
                            <td>{{ $row->bobot }}%</td>
                        </tr>
                    @endforeach
                    <tr></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
