/*
 Navicat Premium Data Transfer

 Source Server         : Stellar_180
 Source Server Type    : SQL Server
 Source Server Version : 10501600
 Source Host           : 192.168.50.180:1433
 Source Catalog        : VAD10
 Source Schema         : dbo

 Target Server Type    : SQL Server
 Target Server Version : 10501600
 File Encoding         : 65001

 Date: 12/06/2020 19:55:51
*/


-- ----------------------------
-- Table structure for DB_PRODUCTOS_USD
-- ----------------------------
IF EXISTS (SELECT * FROM sys.all_objects WHERE object_id = OBJECT_ID(N'[dbo].[DB_PRODUCTOS_USD]') AND type IN ('U'))
	DROP TABLE [dbo].[DB_PRODUCTOS_USD]
GO

CREATE TABLE [dbo].[DB_PRODUCTOS_USD] (
  [id] int  IDENTITY(1,1) NOT NULL,
  [c_TASA_ID] int  NOT NULL,
  [c_FECHA_MOD] datetime  NOT NULL,
  [c_USR_MOD] varchar(50) COLLATE SQL_Latin1_General_CP1_CI_AS  NOT NULL,
  [c_CODIGO] nvarchar(15) COLLATE SQL_Latin1_General_CP1_CI_AS  NOT NULL,
  [c_COSTO_USD] numeric(18,4)  NOT NULL,
  [c_PRECIO_USD] numeric(18,4)  NOT NULL,
  [c_ACTIVO] int DEFAULT 1 NOT NULL
)  
ON [PRIMARY]
GO

ALTER TABLE [dbo].[DB_PRODUCTOS_USD] SET (LOCK_ESCALATION = TABLE)
GO


-- ----------------------------
-- Table structure for DB_TASAS_USD
-- ----------------------------
IF EXISTS (SELECT * FROM sys.all_objects WHERE object_id = OBJECT_ID(N'[dbo].[DB_TASAS_USD]') AND type IN ('U'))
	DROP TABLE [dbo].[DB_TASAS_USD]
GO

CREATE TABLE [dbo].[DB_TASAS_USD] (
  [id] int  IDENTITY(1,1) NOT NULL,
  [fecha] date  NOT NULL,
  [tasa] numeric(18,4)  NOT NULL,
  [modificado_por] varchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NOT NULL,
  [ultima_modificacion] datetime  NULL,
  [activo] int DEFAULT 0 NOT NULL
)  
ON [PRIMARY]
GO

ALTER TABLE [dbo].[DB_TASAS_USD] SET (LOCK_ESCALATION = TABLE)
GO


-- ----------------------------
-- Primary Key structure for table DB_PRODUCTOS_USD
-- ----------------------------
ALTER TABLE [dbo].[DB_PRODUCTOS_USD] ADD CONSTRAINT [PK__DB_PRODU__c_CODIGO] PRIMARY KEY CLUSTERED ([c_CODIGO])
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON)  
ON [PRIMARY]
GO


-- ----------------------------
-- Indexes structure for table DB_TASAS_USD
-- ----------------------------
CREATE UNIQUE NONCLUSTERED INDEX [PK_DB_TASAS_USD]
ON [dbo].[DB_TASAS_USD] (
  [fecha] ASC,
  [tasa] ASC
)
GO


-- ----------------------------
-- Primary Key structure for table DB_TASAS_USD
-- ----------------------------
ALTER TABLE [dbo].[DB_TASAS_USD] ADD CONSTRAINT [PK__DB_TASAS__id] PRIMARY KEY CLUSTERED ([id])
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON)  
ON [PRIMARY]
GO

