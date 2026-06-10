<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });

        $this->deduplicateGroupNames();
        $this->deduplicateGroupMembers();

        Schema::table('groups', function (Blueprint $table) {
            $table->unique('name');
        });

        Schema::table('group_user', function (Blueprint $table) {
            $table->unique(['group_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('group_user', function (Blueprint $table) {
            $table->dropUnique(['group_id', 'user_id']);
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->dropColumn('description');
        });
    }

    private function deduplicateGroupNames(): void
    {
        $duplicates = DB::table('groups')
            ->select('name')
            ->groupBy('name')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('name');

        foreach ($duplicates as $name) {
            $groups = DB::table('groups')
                ->where('name', $name)
                ->orderBy('id')
                ->get(['id']);

            foreach ($groups->skip(1) as $group) {
                DB::table('groups')
                    ->where('id', $group->id)
                    ->update(['name' => $name.' #'.$group->id]);
            }
        }
    }

    private function deduplicateGroupMembers(): void
    {
        $duplicates = DB::table('group_user')
            ->select('group_id', 'user_id')
            ->groupBy('group_id', 'user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $idsToRemove = DB::table('group_user')
                ->where('group_id', $duplicate->group_id)
                ->where('user_id', $duplicate->user_id)
                ->orderBy('id')
                ->pluck('id')
                ->skip(1);

            DB::table('group_user')->whereIn('id', $idsToRemove)->delete();
        }
    }
};
