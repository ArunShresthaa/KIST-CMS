<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Models\Department;
use App\Models\Patient;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('bed_no')
                    ->label('Bed No.')
                    ->required()
                    ->maxLength(255),
                TextInput::make('hos_no')
                    ->label('Hos. No.')
                    ->required()
                    ->maxLength(255),
                TextInput::make('patient_name')
                    ->label('Patient Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('age')
                    ->label('Age')
                    ->required()
                    ->maxLength(20)
                    ->placeholder('e.g., 5D, 3M, 25Y')
                    ->helperText('Use D for days, M for months, Y for years'),
                Select::make('sex')
                    ->label('Sex')
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                        'Other' => 'Other',
                    ])
                    ->required(),
                Select::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->options(Department::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('admitted_date')
                    ->label('Admitted Date')
                    ->required()
                    ->native(false),
                Textarea::make('remarks')
                    ->label('Remarks')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('S.N')
                    ->sortable(),
                TextColumn::make('bed_no')
                    ->label('Bed No.')
                    ->searchable(),
                TextColumn::make('hos_no')
                    ->label('Hos. No.')
                    ->searchable(),
                TextColumn::make('patient_name')
                    ->label('Patient Name')
                    ->searchable(),
                TextColumn::make('age')
                    ->label('Age')
                    ->sortable(),
                TextColumn::make('sex')
                    ->label('Sex')
                    ->searchable(),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable(),
                TextColumn::make('admitted_date')
                    ->label('Admitted Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('remarks')
                    ->label('Remarks')
                    ->limit(50),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
        ];
    }
}
