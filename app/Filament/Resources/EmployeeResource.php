<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Employee Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('employee_code')
                            ->label('Employee ID / NIK')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(50),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->native(false),
                        Forms\Components\Textarea::make('address')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Employment Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('department_id')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('position_id', null)),
                        Forms\Components\Select::make('position_id')
                            ->relationship(
                                'position',
                                'name',
                                fn (Builder $query, callable $get) => $query->when(
                                    $get('department_id'),
                                    fn ($q, $deptId) => $q->where('department_id', $deptId)
                                )
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('manager_id')
                            ->relationship('manager', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn (Employee $record) => "{$record->employee_code} - {$record->first_name} {$record->last_name}")
                            ->searchable()
                            ->preload()
                            ->label('Direct Supervisor / Manager'),
                        Forms\Components\DatePicker::make('join_date')
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('employment_status')
                            ->options([
                                'permanent' => 'Permanent',
                                'contract' => 'Contract',
                                'probation' => 'Probation',
                                'intern' => 'Intern',
                            ])
                            ->default('permanent')
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'terminated' => 'Terminated',
                                'resigned' => 'Resigned',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false),
                    ]),

                Forms\Components\Section::make('User Account Linking')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->unique(ignoreRecord: true)
                            ->label('Linked Login Account'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_code')
                    ->label('NIK')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->state(fn (Employee $record): string => $record->full_name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('position.name')
                    ->label('Position')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('employment_status')
                    ->badge()
                    ->colors([
                        'primary' => 'permanent',
                        'warning' => 'contract',
                        'info' => 'probation',
                        'gray' => 'intern',
                    ]),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => fn ($state): bool => in_array($state, ['terminated', 'resigned']),
                    ]),
                Tables\Columns\TextColumn::make('join_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->relationship('department', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'terminated' => 'Terminated',
                        'resigned' => 'Resigned',
                    ]),
                Tables\Filters\SelectFilter::make('employment_status')
                    ->options([
                        'permanent' => 'Permanent',
                        'contract' => 'Contract',
                        'probation' => 'Probation',
                        'intern' => 'Intern',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
