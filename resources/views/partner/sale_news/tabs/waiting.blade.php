<div id="waiting" class="container-fluid p-0 tab-pane">
    <h2>Waiting</h2>
    <!-- Nội dung của tab Pending -->
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
            @foreach ($data as $item)
            @if ($item->approved == 0)
            <tr>
                <td>
                    <div><span class="badge bg-label-secondary my-1">#{{ $item->sale_new_id }}
                            @if ($item->vip_package_id > 0)
                            <span><i class="fa-solid text-warning fa-star me-1"></i></span>
                            @else
                            @endif
                            <!-- Biểu tượng ngôi sao -->
                        </span>
                    </div>




                </td>
                <td>

                    <div class="row d-flex justify-content-Start text-truncate-3">

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
                    
                    <td> @if($item->status == 1 && $item->approved!=2  )
                    <span class="  text-primary">In stock</span>
                    @else 
                    <span class="  text-danger">Out of stock</span>
                    @endif
                </td>
                <td>
                    <button type="button" class="btn btn-sm text-center text-primary" style="position: relative;"
                        data-bs-toggle="modal" data-bs-target="#modal1{{ $item->sale_new_id }}">
                        <i class="fas fa-eye"></i>
                        <span class="tooltip-text eye">View</span>
                    </button>
                    <div class="modal fade" id="modal1{{ $item->sale_new_id }}" tabindex="-1"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">
                                        Content News </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table">
                                        <tbody>
                                            <tr data-dt-row="2" data-dt-column="2">
                                                <td class="col-3">Product:</td>
                                                <td class="col-9">
                                                    <div
                                                        class="d-flex justify-content-start align-items-center product-name">
                                                        <div class="avatar-wrapper">
                                                            <div class="d-flex flex-wrap">
                                                                @foreach ($item->images
                                                                as $itemIMG)
                                                                <div
                                                                    class="avatar avatar me-4 rounded-2 bg-label-secondary">
                                                                    <img src="{{ $itemIMG->image_name }}"
                                                                        alt="Product-3" class="rounded"
                                                                        style="width: 100%; object-fit: cover;">
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        @if ($item->vip_package_id > 0)
                                                        <span class="badge text-warning"><i
                                                                class="fa-solid text-warning fa-star me-1"
                                                                style="margin-left:50px"></i>
                                                            {{ $item->vippackage->name }}</span>
                                                        @else
                                                        @endif

                                                    </div>

                                </div>
                </td>
            </tr>
            <tr data-dt-row="2" data-dt-column="2">
                <td class="col-3">Title:</td>
                <td class="col-9">
                    <div class="d-flex justify-content-start align-items-center product-name">

                        <div class="d-flex flex-column">
                            <h6 class="mb-0 text-truncate-1">
                                {{ $item->name_product }}</h6>
                            <small class="text-truncate-1">{{ $item->title }}</small>
                        </div>
                    </div>



</div>
</td>
</tr>
<tr data-dt-row="2" data-dt-column="3">
    <td class="col-3">Category:</td>
    <td class="col-9">
        <span class="badge bg-label-secondary">{{ $item->sub_category->category->name_category }}</span>
        <span class="text-muted"> &#8594; </span>
        <span class="badge text-secondary">{{ $item->sub_category->name_sub_category }}</span>
    </td>
</tr>
<tr data-dt-row="2" data-dt-column="6">
    <td class="col-3">Price:</td>
    <td class="col-9"><span>${{ number_format($item->price, 2) }}</span></td>
</tr>

<tr data-dt-row="2" data-dt-column="10">
    <td class="col-3">Create By:</td>
    <td class="col-9"><span>{{ $item->user->full_name }}</span></td>
</tr>


@if ($item->approved == 0)
<tr data-dt-row="2" data-dt-column="8">
    <td class="col-3">Time remaining:</td>
    <td class="col-8 bg-light rounded">
        <div class="d-flex align-items-center" style="font-size: 15px; font-weight:700">
            <i class="fa-regular fa-clock text-danger me-1"></i>
            <!-- Thêm margin-right cho icon để cách ra với chữ -->

            <p class="text-danger mb-0">
                <!-- Sử dụng mb-0 để loại bỏ margin-bottom của đoạn văn -->
                @php
                // Tính số ngày và giờ còn lại đến hết 7 ngày
                $endTime = \Carbon\Carbon::parse(
                $item->created_at,
                )->addDays(7); // Thời gian kết thúc sau 7 ngày
                $remainingDays = floor(
                \Carbon\Carbon::now()->diffInDays($endTime, false),
                ); // Số ngày còn lại (làm tròn xuống số nguyên)
                $remainingHours = floor(
                \Carbon\Carbon::now()->diffInHours($endTime, false) %
                24,
                ); // Số giờ còn lại (làm tròn xuống số nguyên)
                @endphp

                @if ($remainingDays > 0)
                {{ $remainingDays }} day
                @endif

                @if ($remainingHours > 0)
                {{ $remainingHours }} hours
                @endif
            </p>
    </td>
</tr>
@else
<!-- Nếu approved không bằng 0, không hiển thị gì -->
@endif

</div>

<tr data-dt-row="2" data-dt-column="9">
    <td class="col-3">Created At:</td>
    <td class="col-9"><span>{{ date('D, d M Y', strtotime($item->created_at)) }}</span>
    </td>
</tr>
<tr data-dt-row="2" data-dt-column="7">
    <td class="col-3">Status:</td>
    <td class="col-8 bg-light rounded">
        @if ($item->status == 1)
        <span class="badge bg-label-success">In Stock</span>
        @else
        <span class="badge bg-label-secondary">Out of stock</span>
        @endif
    </td>
</tr>


</tbody>
</table>

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
                <form action="{{ route('sale-news-channel.toggleStatus', $item->sale_new_id) }}" method="POST"
                    style="display:inline;">
                    @csrf
                    <button type="submit"
                        class="dropdown-item {{ $item->status == 0 ? 'text-white d-none' : 'd-block' }}">
                        <span><i class="fa-solid fas fa-times-circle me-1 "></i></span>Out of stock
                    </button>
                </form>
            </li>
            <li>
                <form action="{{ route('sale-news-channel.toggleStatus', $item->sale_new_id) }}" method="POST"
                    style="display:inline;">
                    @csrf
                    <button type="submit"
                        class="dropdown-item {{ $item->status == 1 ? 'text-white d-none' : 'd-block' }}">
                        <span><i class="fa-solid fas fa-check-circle me-1"></i></span>In stock</a>
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





<!-- end item -->
</tbody>
</table>
</div>