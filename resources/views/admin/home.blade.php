@extends('admin.layout')

@section('title', '首页')

@section('page-wrapper')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">首页</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">Redis</div>
                <div class="panel-body">
                    <table class="table table-bordered table-hover table-condensed">
                        <tr>
                            <td>版本号(redis_version)</td><td>{{$redis['Server']['redis_version']}}</td>
                            <td>进程ID(process_id)</td><td>{{$redis['Server']['process_id']}}</td>
                        </tr>

                        <tr>
                            <td>运行天数(uptime_in_days)</td><td>{{$redis['Server']['uptime_in_days']}}</td>
                            <td>碎片比率(mem_fragmentation_ratio)</td><td>{{$redis['Memory']['mem_fragmentation_ratio']}}</td>
                        </tr>

                        <tr>
                            <td>分配的内存总量(used_memory_human)</td><td>{{$redis['Memory']['used_memory_human']}}</td>
                            <td>常驻集大小(used_memory_rss_human)</td><td>{{$redis['Memory']['used_memory_rss_human']}}</td>
                        </tr>


                        <tr>
                            <td>命中Key的次数(keyspace_hits)</td><td>{{$redis['Stats']['keyspace_hits']}}</td>
                            <td>没命中Key的次数(keyspace_misses)</td><td>{{$redis['Stats']['keyspace_misses']}}</td>
                        </tr>

                        <tr>
                            <td>Key命中率</td><td>{{round($redis['Stats']['keyspace_hits'] / ($redis['Stats']['keyspace_hits'] + $redis['Stats']['keyspace_misses']), 4) * 100}}%</td>
                            <td>未过期Key数量(expired_keys)</td><td>{{$redis['Stats']['expired_keys']}}</td>
                        </tr>

                    </table>
                </div>
            </div>

        </div>
    </div>

@endsection