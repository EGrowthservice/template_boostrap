<div id="sold" class="container-fluid p-0 tab-pane">
    <h2>Sold</h2>
    <table id="example" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th>Id news</th>
                <th>Title</th>
                <th>Category</th>
                <th>Approve Status</th>
                <th>Status</th>
                <th>Content</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if ($data->isEmpty() || $data->every(fn($item) => !($item->status == 0 || $item->approved == 2)))
            <!-- Không có dữ liệu hoặc tất cả đều không thỏa mãn điều kiện -->
            @else
            <!-- Nội dung bảng, hiển thị các mục thỏa mãn điều kiện -->
            @foreach ($data as $item)
            @if ($item->status == 0 || $item->approved == 2)
            <tr>
                <td>
                    <div><span class="badge bg-label-secondary my-1">#{{ $item->sale_new_id }}
                            @if ($item->vip_package_id > 0)
                            <span><i class="fa-solid text-warning fa-star me-1"></i></span>
                            @else
                            @endif
                        </span></div>
                </td>
                <td>
                    <div class="row d-flex justify-content-start text-truncate-3">
                        {{ $item->title }}
                    </div>
                </td>
                <td class="bg-light rounded">
                    <span class="badge bg-label-primary">
                        {{ $item->sub_category->category->name_category }} </span>
                    <span class="text-muted"> &#8594; </span>
                    <span class="badge text-secondary">
                        {{ $item->sub_category->name_sub_category }}</span>
                </td>
                <td class="bg-light rounded">
                    @if ($item->approved == 0)
                    <span class="badge bg-label-warning">Waiting</span>
                    @elseif($item->approved == 1)
                    <span class="badge bg-label-success">Approved</span>
                    @elseif($item->approved == 2)
                    <span class="badge bg-label-danger">Rejected</span>
                    @endif
                </td>
                <td>
                    @if($item->status == 1 && $item->approved != 2 )
                    <span class="text-primary">In stock</span>
                    @else
                    <span class="text-danger">Out of stock</span>
                    @endif
                </td>
                <td>
                    <button type="button" class="btn btn-sm text-center text-primary" style="position: relative;"
                        data-bs-toggle="modal" data-bs-target="#modal6{{ $item->sale_new_id }}">
                        <i class="fas fa-eye"></i>
                        <span class="tooltip-text eye">View</span>
                    </button>
                    <div class="modal fade" id="modal6{{ $item->sale_new_id }}" tabindex="-1"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Content News</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Nội dung modal với thông tin sản phẩm, giá, trạng thái, ... -->
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow"
                            data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <form action="{{ route('sale-news-channel.toggleStatus', $item->sale_new_id) }}"
                                    method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit"
                                        class="dropdown-item {{ $item->status == 0 ? 'text-white d-none' : 'd-block' }}">
                                        <span><i class="fa-solid fas fa-times-circle me-1 "></i></span>Out of stock
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form action="{{ route('sale-news-channel.toggleStatus', $item->sale_new_id) }}"
                                    method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit"
                                        class="dropdown-item {{ $item->status == 1 ? 'text-white d-none' : 'd-block' }}">
                                        <span><i class="fa-solid fas fa-check-circle me-1"></i></span>In stock
                                    </button>
                                </form>
                            </li>
                            <li>
                                <a onclick="confirmDelete(event, {{ $item->sale_new_id }})">
                                    <form id="delete-form-{{ $item->sale_new_id }}"
                                        action="{{ route('sale_news.destroy', $item->sale_new_id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item">
                                            <span><i class="fa-solid fa-trash me-1"></i></span>Delete
                                        </button>
                                    </form>
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            @endif
            @endforeach
            @endif
        </tbody>
    </table>

    @if ($data->isEmpty() || $data->every(fn($item) => !($item->status == 0 || $item->approved == 2)))
    <p class="mt-1 text-center">No data available in table!</p>
    @endif
</div>