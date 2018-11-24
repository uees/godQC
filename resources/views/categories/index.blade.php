@extends('layouts.app')

@section('title', '产品分类管理')

@section('content')
    <a class="btn btn-default" href="{{ route('categories.create') }}" role="button">添加</a>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>名称</th>
                <th>编码</th>
                <th>备注</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $loop->iteration }}#</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->slug }}</td>
                    <td>{{ $category->memo }}</td>
                    <td>
                        <a href="{{ route('categories.edit', ['category' => $category]) }}" role="button">编辑</a> |
                        <a href="{{ route('categories.destroy', ['category' => $category]) }}" role="button">删除</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
