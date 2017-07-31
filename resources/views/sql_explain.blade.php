<html>
<head>
    <title>SQL分析</title>
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
    <table class="table table-striped table-bordered table-hover table-condensed table-responsive">
        <thead>
        <tr>
            <th>SQL序号</th>
            <th>id</th>
            <th>select_type</th>
            <th>table</th>
            <th>type</th>
            <th>possible_keys</th>
            <th>key</th>
            <th>key_len</th>
            <th>ref</th>
            <th>rows</th>
            <th>Extra</th>
        </tr>
        </thead>
        <tbody>
        @foreach($rows as $index => $row)
        <tr>
            <td rowspan="2" class="text-center"><b>{{$index + 1}}</b></td>
            <td colspan="10">{{$row['query']}}</td>
        </tr>

        <tr>
            <td>{{$row['id']}}</td>
            <td>{{$row['select_type']}}</td>
            <td>{{$row['table']}}</td>
            <td>{{$row['type']}}</td>
            <td>{{$row['possible_keys']}}</td>
            <td>{{$row['key']}}</td>
            <td>{{$row['key_len']}}</td>
            <td>{{$row['ref']}}</td>
            <td>{{$row['rows']}}</td>
            <td>{{$row['Extra']}}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>