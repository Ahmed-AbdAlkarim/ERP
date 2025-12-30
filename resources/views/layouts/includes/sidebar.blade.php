<!-- Sidebar -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <!-- Logo -->
  <div class="app-brand demo">
    <a href="{{ route('dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        
      </span>
      <span class="app-brand-text demo menu-text fw-bold">click store</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
      <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">

    @can('view_dashboard')
    <!-- Dashboard -->
    <li class="menu-item {{ request()->routeIs('dashboard') ? 'active open' : '' }}">
      <a href="{{ route('dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-home"></i>
        <div>لوحة التحكم</div>
      </a>
    </li>
    @endcan

    <!-- Apps Header -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">التطبيقات</span>
    </li>
    @can('view_products')
    <!-- Products -->
    <li class="menu-item {{ request()->is('admin/products*') ? 'active' : '' }}">
      <a href="{{ route('admin.products.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-box"></i>
        <div>المنتجات</div>
      </a>
    </li>
    @endcan

    @can('view_customers')
    <!-- Customers -->
    <li class="menu-item {{ request()->is('admin/customers*') ? 'active' : '' }}">
      <a href="{{ route('admin.customers.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-users"></i>
        <div>العملاء</div>
      </a>
    </li>
    @endcan

    @can('view_suppliers')
    <!-- Suppliers -->
    <li class="menu-item {{ request()->is('admin/suppliers*') ? 'active' : '' }}">
      <a href="{{ route('admin.suppliers.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-truck-delivery"></i>
        <div>الموردين</div>
      </a>
    </li>
    @endcan

    @can('view_purchase_invoice')
    <!-- Purchase Invoices -->
    <li class="menu-item {{ request()->is('admin/purchase_invoices*') ? 'active' : '' }}">
      <a href="{{ route('admin.purchase_invoices.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-receipt"></i>
        <div>فواتير الشراء</div>
      </a>
    </li>
    @endcan

    @can('view_sales_invoice')
    <!-- Sales Invoices -->
    <li class="menu-item {{ request()->is('admin/sales-invoices*') ? 'active' : '' }}">
      <a href="{{ route('admin.sales-invoices.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
        <div>فواتير البيع</div>
      </a>
    </li>
    @endcan

    @can('view_maintenances')
    <!-- Maintenance Requests -->
    <li class="menu-item {{ request()->is('admin/maintenances*') ? 'active' : '' }}">
      <a href="{{ route('admin.maintenances.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-tools"></i>
        <div>طلبات الصيانة</div>
      </a>
    </li>
    @endcan

    @can('view_inventory')
    <!-- Inventory -->
    <li class="menu-item {{ request()->is('admin/inventory*') ? 'active' : '' }}">
      <a href="{{ route('admin.inventory.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-package"></i>
        <div>المخزون</div>
      </a>
    </li>
    @endcan
    @can('view_profits')
    <!-- Profit Reports -->
    <li class="menu-item {{ request()->is('admin/profit-reports*') ? 'active' : '' }}">
      <a href="{{ route('admin.profit-reports.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-trending-up"></i>
        <div>تقارير الأرباح</div>
      </a>
    </li>
    @endcan

    @can('view_expenses')
    <!-- Expenses Management -->
    <li class="menu-item {{ request()->is('admin/expenses*') ? 'active' : '' }}">
      <a href="{{ route('admin.expenses.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-currency-dollar"></i>
        <div>إدارة المصروفات</div>
      </a>
    </li>
    @endcan

    @can('view_cashbox')
    <!-- Cashbox Management -->
    <li class="menu-item {{ request()->is('admin/cashboxes*') ? 'active' : '' }}">
      <a href="{{ route('admin.cashboxes.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-cash"></i>
        <div>إدارة الخزائن</div>
      </a>
    </li>
    @endcan

    @can('view_debts')
    <!-- Debts Management -->
    <li class="menu-item {{ request()->is('admin/debts*') ? 'active' : '' }}">
      <a href="{{ route('admin.debts.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-report-money"></i>
        <div>إدارة المديونيات</div>
      </a>
    </li>
    @endcan

    @can('view_users')
    <!-- Users Management -->
    <li class="menu-item {{ request()->is('admin/users*') ? 'active' : '' }}">
      <a href="{{ route('admin.users.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-user-circle"></i>
        <div>إدارة المستخدمين</div>
      </a>
    </li>
    @endcan
    
    @can('view_roles')
    <!-- Roles Management -->
    <li class="menu-item {{ request()->is('admin/roles*') ? 'active' : '' }}">
      <a href="{{ route('admin.roles.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-shield-check"></i>
        <div>إدارة الأدوار</div>
      </a>
    </li>
    @endcan

    @can('view_permission')
    <!-- permissions Management -->
    <li class="menu-item {{ request()->is('admin/permissions*') ? 'active' : '' }}">
      <a href="{{ route('admin.permissions.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-key"></i>
        <div>إدارة الصلاحيات</div>
      </a>
    </li>
    @endcan

  </ul>
</aside>
<!-- /Sidebar -->
