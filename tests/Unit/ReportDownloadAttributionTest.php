<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\ClientReportController;
use App\Models\User;
use App\Services\BusinessReportService;
use App\Services\PdfBranding;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class ReportDownloadAttributionTest extends TestCase
{
    public function test_business_report_uses_the_current_downloaders_account_name(): void
    {
        $service = Mockery::mock(BusinessReportService::class);
        $service->shouldReceive('generate')->twice()->andReturn(['preparedAt' => now('Asia/Manila')]);

        foreach (['Alexandra Santos', 'Miguel Reyes'] as $name) {
            $admin = new User(['name' => $name, 'role' => User::ROLE_ADMIN]);
            $request = Request::create('/admin/reports/business/download', 'GET', ['downloadedByName' => 'Spoofed name']);
            $request->setUserResolver(fn () => $admin);
            $pdf = Mockery::mock(\Barryvdh\DomPDF\PDF::class);
            Pdf::shouldReceive('loadView')->once()->with('admin.reports.business', Mockery::on(
                fn ($data) => $data['downloadedByName'] === $name && str_starts_with($data['logoDataUri'], 'data:image/jpeg;base64,')
            ))->andReturn($pdf);
            $pdf->shouldReceive('setPaper')->once()->with('a4', 'landscape')->andReturnSelf();
            $pdf->shouldReceive('download')->once()->andReturn(new Response('%PDF-test', 200));

            $response = app(ClientReportController::class)->downloadBusiness($request, $service, app(PdfBranding::class));
            $this->assertSame(200, $response->getStatusCode());
        }
    }

    public function test_verification_is_only_in_settings_and_settings_stays_active(): void
    {
        $this->actingAs(new User(['name' => 'Sample Client', 'role' => User::ROLE_CLIENT]));
        $sidebar = view('partials.client-sidebar', ['active' => 'settings'])->render();
        $this->assertStringNotContainsString(route('profile.verification.edit'), $sidebar);
        $this->assertStringContainsString('href="'.route('profile.edit').'"', $sidebar);
        $this->assertMatchesRegularExpression('/class="nav-link active"[^>]*href="[^"]*\/profile"[^>]*aria-current="page"/', $sidebar);
        $settings = view('partials.settings-tabs', ['activeSettings' => 'verification'])->render();
        $this->assertStringContainsString(route('profile.verification.edit'), $settings);
        $this->assertStringContainsString('Verification', $settings);
    }
}
