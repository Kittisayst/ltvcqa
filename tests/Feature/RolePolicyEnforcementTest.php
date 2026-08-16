<?php

it('blocks department-staff from the shield roles resource', function (): void {
    actingAsDepartmentStaff();

    $this->get('/admin/shield/roles')->assertForbidden();
});

it('allows super_admin to view the shield roles resource', function (): void {
    actingAsSuperAdmin();

    $this->get('/admin/shield/roles')->assertOk();
});
