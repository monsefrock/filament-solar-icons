<?php

/**
 * Comprehensive Solar Icons Usage Examples for Filament v3 and v4
 * 
 * This file demonstrates various ways to use Solar icons in Filament applications.
 * Copy and adapt these examples to your own resources, forms, and components.
 */

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Navigation\NavigationItem;
use Filament\Widgets\StatsOverviewWidget\Stat;

// For Filament v4 - Import the Solar Icon enum
use Monsefeledrisse\FilamentSolarIcons\SolarIcon;

class ExampleResource extends Resource
{
    // Navigation Icon
    // v3: protected static ?string $navigationIcon = 'solar-linear-users';
    // v4:
    protected static ?string $navigationIcon = SolarIcon::LinearUsers->value;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Text Input with prefix icon
                Forms\Components\TextInput::make('name')
                    ->label('Full Name')
                    // v3: ->prefixIcon('solar-outline-user')
                    // v4:
                    ->prefixIcon(SolarIcon::OutlineUser)
                    ->required(),

                // Email with mail icon
                Forms\Components\TextInput::make('email')
                    ->email()
                    // v3: ->prefixIcon('solar-linear-letter')
                    // v4:
                    ->prefixIcon(SolarIcon::LinearMail)
                    ->required(),

                // Phone with phone icon
                Forms\Components\TextInput::make('phone')
                    // v3: ->prefixIcon('solar-outline-phone')
                    // v4:
                    ->prefixIcon(SolarIcon::OutlinePhone),

                // Password with show/hide functionality
                Forms\Components\TextInput::make('password')
                    ->password()
                    // v3: ->suffixIcon('solar-outline-eye')
                    // v4:
                    ->suffixIcon(SolarIcon::OutlineEye)
                    ->required(),

                // Select with settings icon
                Forms\Components\Select::make('role')
                    ->options([
                        'admin' => 'Administrator',
                        'user' => 'User',
                        'moderator' => 'Moderator',
                    ])
                    // v3: ->prefixIcon('solar-bold-shield-user')
                    // v4:
                    ->prefixIcon(SolarIcon::Shield)
                    ->required(),

                // Toggle with appropriate icons
                Forms\Components\Toggle::make('is_active')
                    ->label('Active Status')
                    // v3: ->onIcon('solar-bold-check-circle')
                    // v3: ->offIcon('solar-outline-close-circle')
                    // v4:
                    ->onIcon(SolarIcon::Success)
                    ->offIcon(SolarIcon::OutlineError),

                // Date picker with calendar icon
                Forms\Components\DatePicker::make('birth_date')
                    // v3: ->prefixIcon('solar-linear-calendar')
                    // v4:
                    ->prefixIcon(SolarIcon::LinearCalendar),

                // File upload with upload icon
                Forms\Components\FileUpload::make('avatar')
                    // v3: ->prefixIcon('solar-outline-camera')
                    // v4:
                    ->prefixIcon(SolarIcon::OutlineCamera)
                    ->image(),

                // Rich text editor
                Forms\Components\RichEditor::make('bio')
                    ->label('Biography'),

                // Repeater with appropriate action icons
                Forms\Components\Repeater::make('social_links')
                    ->schema([
                        Forms\Components\TextInput::make('platform')
                            // v3: ->prefixIcon('solar-linear-share')
                            // v4:
                            ->prefixIcon(SolarIcon::LinearShare),
                        Forms\Components\TextInput::make('url')
                            ->url(),
                    ])
                    // v4 action icons are automatically handled by Filament
                    ->addActionLabel('Add Social Link'),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // Text column with user icon
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    // v3: ->icon('solar-linear-user')
                    // v4:
                    ->icon(SolarIcon::LinearUser)
                    ->searchable()
                    ->sortable(),

                // Email column with mail icon
                Tables\Columns\TextColumn::make('email')
                    // v3: ->icon('solar-outline-letter')
                    // v4:
                    ->icon(SolarIcon::OutlineMail)
                    ->searchable(),

                // Status column with boolean icons
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    // v3: ->trueIcon('solar-bold-check-circle')
                    // v3: ->falseIcon('solar-outline-close-circle')
                    // v4:
                    ->trueIcon(SolarIcon::Success)
                    ->falseIcon(SolarIcon::OutlineError)
                    ->trueColor('success')
                    ->falseColor('danger'),

                // Role column with shield icon
                Tables\Columns\TextColumn::make('role')
                    // v3: ->icon('solar-outline-shield-user')
                    // v4:
                    ->icon(SolarIcon::OutlineShield)
                    ->badge(),

                // Date column with calendar icon
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    // v3: ->icon('solar-linear-calendar')
                    // v4:
                    ->icon(SolarIcon::LinearCalendar)
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    // v3: ->trueIcon('solar-bold-check-circle')
                    // v3: ->falseIcon('solar-outline-close-circle')
                    // v4:
                    ->trueIcon(SolarIcon::Success)
                    ->falseIcon(SolarIcon::OutlineError),
            ])
            ->actions([
                // View action
                Tables\Actions\ViewAction::make()
                    // v3: ->icon('solar-outline-eye')
                    // v4:
                    ->icon(SolarIcon::OutlineEye),

                // Edit action
                Tables\Actions\EditAction::make()
                    // v3: ->icon('solar-outline-pen')
                    // v4:
                    ->icon(SolarIcon::OutlineEdit),

                // Delete action
                Tables\Actions\DeleteAction::make()
                    // v3: ->icon('solar-outline-trash-bin-minimalistic')
                    // v4:
                    ->icon(SolarIcon::OutlineDelete),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        // v3: ->icon('solar-bold-trash-bin-minimalistic')
                        // v4:
                        ->icon(SolarIcon::Delete),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

// Custom Actions Examples
class CustomActionsExample
{
    public function getHeaderActions(): array
    {
        return [
            // Export action
            Actions\Action::make('export')
                ->label('Export Users')
                // v3: ->icon('solar-linear-download')
                // v4:
                ->icon(SolarIcon::LinearDownload)
                ->action(function () {
                    // Export logic
                }),

            // Import action
            Actions\Action::make('import')
                ->label('Import Users')
                // v3: ->icon('solar-linear-upload')
                // v4:
                ->icon(SolarIcon::LinearUpload)
                ->action(function () {
                    // Import logic
                }),

            // Send notification action
            Actions\Action::make('notify')
                ->label('Send Notification')
                // v3: ->icon('solar-bold-bell-bing')
                // v4:
                ->icon(SolarIcon::Notification)
                ->action(function () {
                    // Notification logic
                }),

            // Generate report action
            Actions\Action::make('report')
                ->label('Generate Report')
                // v3: ->icon('solar-outline-document-text')
                // v4:
                ->icon(SolarIcon::OutlineFile)
                ->action(function () {
                    // Report generation logic
                }),
        ];
    }
}

// Navigation Examples
class NavigationExample
{
    public function getNavigationItems(): array
    {
        return [
            // Dashboard
            NavigationItem::make('Dashboard')
                // v3: ->icon('solar-bold-home')
                // v4:
                ->icon(SolarIcon::Home)
                ->url('/dashboard')
                ->isActiveWhen(fn (): bool => request()->routeIs('dashboard')),

            // Users
            NavigationItem::make('Users')
                // v3: ->icon('solar-linear-users')
                // v4:
                ->icon(SolarIcon::LinearUsers)
                ->url('/users'),

            // Analytics
            NavigationItem::make('Analytics')
                // v3: ->icon('solar-bold-chart')
                // v4:
                ->icon(SolarIcon::Chart)
                ->url('/analytics'),

            // Settings
            NavigationItem::make('Settings')
                // v3: ->icon('solar-outline-settings')
                // v4:
                ->icon(SolarIcon::OutlineSettings)
                ->url('/settings'),

            // Reports
            NavigationItem::make('Reports')
                // v3: ->icon('solar-linear-document-text')
                // v4:
                ->icon(SolarIcon::LinearFile)
                ->url('/reports'),
        ];
    }
}

// Widget Examples
class WidgetExample
{
    public function getStats(): array
    {
        return [
            // Total users stat
            Stat::make('Total Users', '1,234')
                // v3: ->icon('solar-bold-users')
                // v4:
                ->icon(SolarIcon::Users)
                ->description('Active users in the system')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            // Revenue stat
            Stat::make('Revenue', '$12,345')
                // v3: ->icon('solar-linear-dollar-minimalistic')
                // v4:
                ->icon(SolarIcon::LinearMoney)
                ->description('Monthly revenue')
                ->color('primary'),

            // Orders stat
            Stat::make('Orders', '567')
                // v3: ->icon('solar-outline-cart-large-2')
                // v4:
                ->icon(SolarIcon::OutlineCart)
                ->description('Orders this month')
                ->color('warning'),

            // Messages stat
            Stat::make('Messages', '89')
                // v3: ->icon('solar-linear-chat-round-dots')
                // v4:
                ->icon(SolarIcon::LinearChat)
                ->description('Unread messages')
                ->color('info'),
        ];
    }
}
